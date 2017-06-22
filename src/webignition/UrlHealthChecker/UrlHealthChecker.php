<?php

namespace webignition\UrlHealthChecker;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface as HttpRequest;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException as HttpConnectException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Url as GuzzleUrl;
use GuzzleHttp\Query as GuzzleQuery;
use webignition\GuzzleHttp\Exception\CurlException\Factory as CurlExceptionFactory;
use webignition\GuzzleHttp\Exception\CurlException\Exception as CurlException;


class UrlHealthChecker {

    const HTTP_STATUS_CODE_OK = 200;
    const BAD_REQUEST_LIMIT = 3;

    /**
     *
     * @var int
     */
    private $badRequestCount = 0;


    /**
     *
     * @var Configuration
     */
    private $configuration;


    /**
     *
     * @return Configuration
     */
    public function getConfiguration() {
        if (is_null($this->configuration)) {
            $this->configuration = new Configuration();
        }

        return $this->configuration;
    }

    /**
     * @param $url
     * @return LinkState
     * @throws null
     */
    public function check($url) {
        $requests = $this->buildRequestSet($url);

        try {
            foreach ($requests as $request) {
                $response = $this->getHttpResponse($request);

                if ($response->getStatusCode() === self::HTTP_STATUS_CODE_OK) {
                    return new LinkState(LinkState::TYPE_HTTP, $response->getStatusCode());
                }
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
     *
     * @param string $url
     * @return HttpRequest[]
     */
    private function buildRequestSet($url) {
        $useEncodingOptions = ($this->getConfiguration()->getToggleUrlEncoding())
            ? array(true, false)
            : array(true);

        $requests = array();

        $userAgentSelection = $this->getConfiguration()->getUserAgentSelectionForRequest();

        foreach ($userAgentSelection as $userAgent) {
            foreach ($this->getConfiguration()->getHttpMethodList() as $methodIndex => $method) {
                foreach ($useEncodingOptions as $useEncoding) {
                    $requestUrl = GuzzleUrl::fromString($url);

                    $requestUrl->getQuery()->setEncodingType($useEncoding ? GuzzleQuery::RFC3986 : false);

                    $request = $this->getConfiguration()->getHttpClient()->createRequest(
                        'GET',
                        $requestUrl
                    );

                    $request->setHeader('user-agent', $userAgent);

                    if ($this->getConfiguration()->hasReferrer()) {
                        $request->setHeader('Referer', $this->getConfiguration()->getReferrer());
                    }

                    $requests[] = $request;
                }
            }
        }

        return $requests;
    }


    /**
     * @param HttpRequest $request
     * @return \GuzzleHttp\Message\ResponseInterface|null
     * @throws \webignition\GuzzleHttp\Exception\CurlException\Exception
     */
    private function getHttpResponse(HttpRequest $request) {
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
        } catch (\InvalidArgumentException $e) {
            if (substr_count($e->getMessage(), 'unable to parse malformed url')) {
                $curlException = $this->getCurlMalformedUrlException();
                throw $curlException;
            }
        } catch (HttpConnectException $connectException) {
            throw $connectException;
        } catch (RequestException $requestException) {
            $isCurlExceptionMessage =
                substr($requestException->getMessage(), 0, strlen('cURL error')) == 'cURL error';

            if (!$requestException->hasResponse() && $isCurlExceptionMessage) {
                $connectException = new HttpConnectException(
                    $requestException->getMessage(),
                    $requestException->getRequest(),
                    $requestException->getResponse(),
                    $requestException->getPrevious()
                );

                throw $connectException;
            }

            $this->badRequestCount++;

            if ($this->isBadRequestLimitReached()) {
                return $requestException->getResponse();
            }

            return $this->getHttpResponse($request);
        }
    }

    /**
     *
     * @return boolean
     */
    private function isBadRequestLimitReached() {
        if ($this->getConfiguration()->getRetryOnBadResponse() === false) {
            return true;
        }

        return $this->badRequestCount > self::BAD_REQUEST_LIMIT - 1;
    }


    /**
     *
     * @return CurlException
     */
    private function getCurlMalformedUrlException() {
        return new CurlException(
            Configuration::CURL_MALFORMED_URL_MESSAGE,
            Configuration::CURL_MALFORMED_URL_CODE
        );
    }

}