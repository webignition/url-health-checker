<?php

namespace webignition\UrlHealthChecker;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface as HttpRequest;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException as HttpConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Message\ResponseInterface;
use webignition\GuzzleHttp\Exception\CurlException\Factory as CurlExceptionFactory;
use webignition\GuzzleHttp\Exception\CurlException\Exception as CurlException;

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
     * @return Configuration
     */
    public function getConfiguration()
    {
        if (is_null($this->configuration)) {
            $this->configuration = new Configuration([]);
        }

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
     * @param string $url
     *
     * @return LinkState
     */
    public function check($url)
    {
        $requestFactory = new RequestSetFactory();
        $requestFactory->setConfiguration($this->getConfiguration());

        try {
            $requests = $requestFactory->create($url);

            foreach ($requests as $request) {
                $response = $this->getHttpResponse($request);

                if ($response->getStatusCode() === self::HTTP_STATUS_CODE_OK) {
                    return new LinkState(LinkState::TYPE_HTTP, $response->getStatusCode());
                }
            }
        } catch (\InvalidArgumentException $invalidArgumentException) {
            if (substr_count($invalidArgumentException->getMessage(), 'malformed url')) {
                return new LinkState(
                    LinkState::TYPE_CURL,
                    Configuration::CURL_MALFORMED_URL_CODE
                );
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
     * @param HttpRequest $request
     * @throws CurlException
     *
     * @return ResponseInterface|null
     */
    private function getHttpResponse(HttpRequest $request)
    {
        try {
            return $this->getConfiguration()->getHttpClient()->send($request);
        } catch (TooManyRedirectsException $tooManyRedirectsException) {
            return $this->getConfiguration()->getHttpClientHistory()->getLastResponse();
        } catch (BadResponseException $badResponseException) {
            $this->badRequestCount++;

            if ($this->isBadRequestLimitReached()) {
                return $badResponseException->getResponse();
            }

            return $this->getHttpResponse($request);
        } catch (HttpConnectException $connectException) {
            throw $connectException;
        } catch (RequestException $requestException) {
            // #1815
            // #1816
            // Workaround for a very, very, very small number of GET requests failing with the below exception message
            // and with an exception code of zero.
            // In such cases the request is not lacking a body.
            // Will have to investigate further after upgrading to guzzle6
            $noRequestBodyFailureMessage = 'No response was received for a request with no body. '
                .'This could mean that you are saturating your network.';

            if ($noRequestBodyFailureMessage === $requestException->getMessage()) {
                return new Response(200);
            }

            if ($this->isCurlException($requestException)) {
                throw new HttpConnectException(
                    $requestException->getMessage(),
                    $requestException->getRequest(),
                    $requestException->getResponse(),
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
}
