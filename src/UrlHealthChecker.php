<?php

namespace webignition\UrlHealthChecker;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException as HttpConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use webignition\GuzzleHttp\Exception\CurlException\Factory as CurlExceptionFactory;
use webignition\HttpHistoryContainer\Container as HttpHistoryContainer;

class UrlHealthChecker
{
    const HTTP_STATUS_CODE_OK = 200;
    const BAD_REQUEST_LIMIT = 3;
    const CURL_EXCEPTION_MESSAGE_PREFIX = 'cURL error';

    /**
     * @var int
     */
    private $badRequestCount = 0;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var HttpHistoryContainer
     */
    private $httpHistoryContainer;

    public function __construct()
    {
        $this->configuration = new Configuration();
        $this->httpClient = new HttpClient();
        $this->httpHistoryContainer = new HttpHistoryContainer();

        $this->setHistoryMiddlewareOnHttpClient();
    }

    private function setHistoryMiddlewareOnHttpClient()
    {
        $httpHistory = Middleware::history($this->httpHistoryContainer);

        /* @var HandlerStack $httpClientHandler */
        $httpClientHandler = $this->httpClient->getConfig('handler');
        $httpClientHandler->push($httpHistory);
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param HttpClient $httpClient
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->setHistoryMiddlewareOnHttpClient();
    }

    /**
     * @param string $url
     *
     * @return LinkState
     *
     * @throws GuzzleException
     */
    public function check($url)
    {
        $response = null;

        try {
            $requests = $this->createRequestSet($url);

            foreach ($requests as $request) {
                $response = $this->getHttpResponse($request);

                if ($response->getStatusCode() === self::HTTP_STATUS_CODE_OK) {
                    return new LinkState(LinkState::TYPE_HTTP, $response->getStatusCode());
                }
            }
        } catch (\InvalidArgumentException $invalidArgumentException) {
            if (substr_count($invalidArgumentException->getMessage(), 'Unable to parse URI')) {
                return new LinkState(LinkState::TYPE_CURL, Configuration::CURL_MALFORMED_URL_CODE);
            }
        } catch (HttpConnectException $connectException) {
            $curlExceptionFactory = new CurlExceptionFactory();

            if ($curlExceptionFactory::isCurlException($connectException)) {
                $curlException = $curlExceptionFactory::fromConnectException($connectException);
                return new LinkState(LinkState::TYPE_CURL, $curlException->getCurlCode());
            }
        }

        return new LinkState(LinkState::TYPE_HTTP, $response->getStatusCode());
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws GuzzleException
     */
    private function getHttpResponse(RequestInterface $request)
    {
        try {
            return $this->httpClient->send($request);
        } catch (TooManyRedirectsException $tooManyRedirectsException) {
            return $this->httpHistoryContainer->getLastResponse();
        } catch (BadResponseException $badResponseException) {
            $this->badRequestCount++;

            if ($this->isBadRequestLimitReached()) {
                return $badResponseException->getResponse();
            }

            return $this->getHttpResponse($request);
        } catch (HttpConnectException $connectException) {
            throw $connectException;
        } catch (RequestException $requestException) {
            if ($this->isCurlException($requestException)) {
                throw new HttpConnectException(
                    $requestException->getMessage(),
                    $requestException->getRequest(),
                    $requestException->getPrevious()
                );
            }

            $this->badRequestCount++;

            if ($this->isBadRequestLimitReached()) {
                return $requestException->getResponse();
            }

            return $this->getHttpResponse($request);
        }
    }

    /**
     * @return boolean
     */
    private function isBadRequestLimitReached()
    {
        if ($this->getConfiguration()->getRetryOnBadResponse() === false) {
            return true;
        }

        return $this->badRequestCount > self::BAD_REQUEST_LIMIT - 1;
    }

    /**
     * @param RequestException $requestException
     *
     * @return bool
     */
    private function isCurlException(RequestException $requestException)
    {
        return substr(
            $requestException->getMessage(),
            0,
            strlen(self::CURL_EXCEPTION_MESSAGE_PREFIX)
        ) == self::CURL_EXCEPTION_MESSAGE_PREFIX;
    }

    /**
     * @param string $url
     *
     * @return RequestInterface[]
     */
    private function createRequestSet($url)
    {
        $httpMethodList = $this->configuration->getHttpMethodList();
        $userAgentSelection = $this->getUserAgentSelection();
        $referrer = $this->configuration->getReferrer();

        $requests = [];

        foreach ($userAgentSelection as $userAgent) {
            foreach ($httpMethodList as $methodIndex => $method) {
                $headers = [
                    'user-agent' => $userAgent,
                ];

                if (!empty($referrer)) {
                    $headers['referer'] = $referrer;
                }

                $requests[] = new Request($method, $url, $headers);
            }
        }

        return $requests;
    }

    /**
     * @return string[]
     */
    private function getUserAgentSelection()
    {
        $configurationUserAgents = $this->configuration->getUserAgents();

        if (!empty($configurationUserAgents)) {
            return $configurationUserAgents;
        }

        $httpClientHeaders = $this->httpClient->getConfig('headers');

        return [
            $httpClientHeaders['User-Agent'],
        ];
    }
}
