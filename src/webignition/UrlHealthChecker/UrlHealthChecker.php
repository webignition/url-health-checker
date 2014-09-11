<?php

namespace webignition\UrlHealthChecker;

use Composer\Config;
use Guzzle\Common\Exception\InvalidArgumentException;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\CurlException;
use Guzzle\Http\Exception\TooManyRedirectsException;
use Guzzle\Plugin\History\HistoryPlugin;
use webignition\Cookie\UrlMatcher\UrlMatcher;
use Guzzle\Http\Message\Request as GuzzleRequest;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Http\Url as GuzzleUrl;

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
     *
     * @param string $url
     * @return LinkState
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
        } catch (CurlException $curlException) {
            return new LinkState(LinkState::TYPE_CURL, $curlException->getErrorNo());
        }

        return new LinkState(LinkState::TYPE_HTTP, $response->getStatusCode());
    }


    /**
     *
     * @param string $url
     * @return \Guzzle\Http\Message\Request[]
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
                    $requestUrl = GuzzleUrl::factory($url);
                    $requestUrl->getQuery()->useUrlEncoding($useEncoding);

                    $request = clone $this->getConfiguration()->getBaseRequest();
                    $request->setUrl($requestUrl);
                    $request->setHeader('user-agent', $userAgent);

                    if ($this->getConfiguration()->hasReferrer()) {
                        $request->setHeader('Referer', $this->getConfiguration()->getReferrer());
                    }

                    $this->setRequestCookies($request);

                    $requests[] = $request;
                }
            }
        }

        return $requests;
    }


    /**
     * @param GuzzleRequest $request
     */
    private function setRequestCookies(GuzzleRequest $request) {
        if (!is_null($request->getCookies())) {
            foreach ($request->getCookies() as $name => $value) {
                $request->removeCookie($name);
            }
        }

        $cookieUrlMatcher = new UrlMatcher();

        foreach ($this->getConfiguration()->getCookies() as $cookie) {
            if ($cookieUrlMatcher->isMatch($cookie, $request->getUrl())) {
                $request->addCookie($cookie['name'], $cookie['value']);
            }
        }
    }


    /**
     * @param GuzzleRequest $request
     * @return GuzzleResponse|null
     * @throws \Guzzle\Http\Exception\CurlException
     */
    private function getHttpResponse(GuzzleRequest $request) {
        try {
            return $request->send();
        } catch (TooManyRedirectsException $tooManyRedirectsException) {
            return $this->getHttpClientHistory()->getLastResponse();
        } catch (BadResponseException $badResponseException) {
            $this->badRequestCount++;

            if ($this->isBadRequestLimitReached()) {
                return $badResponseException->getResponse();
            }

            return $this->getHttpResponse($request);
        } catch (InvalidArgumentException $e) {
            if (substr_count($e->getMessage(), 'unable to parse malformed url')) {
                $curlException = $this->getCurlMalformedUrlException();
                throw $curlException;
            }
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
     * @return HistoryPlugin
     */
    private function getHttpClientHistory() {
        $requestSentListeners = $this->getConfiguration()->getBaseRequest()->getEventDispatcher()->getListeners('request.sent');
        foreach ($requestSentListeners as $requestSentListener) {
            if ($requestSentListener[0] instanceof HistoryPlugin) {
                return $requestSentListener[0];
            }
        }

        return null;
    }


    /**
     *
     * @return CurlException
     */
    private function getCurlMalformedUrlException() {
        $curlException = new CurlException();
        $curlException->setError(Configuration::CURL_MALFORMED_URL_MESSAGE, Configuration::CURL_MALFORMED_URL_CODE);
        return $curlException;
    }


}