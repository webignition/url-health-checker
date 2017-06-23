<?php

namespace webignition\UrlHealthChecker;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\History as HttpHistorySubscriber;

class Configuration
{
    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_GET = 'GET';

    const CURL_MALFORMED_URL_CODE = 3;
    const CURL_MALFORMED_URL_MESSAGE = 'The URL was not properly formatted.';

    const CONFIG_KEY_USER_AGENTS = 'user-agents';
    const CONFIG_KEY_HTTP_METHOD_LIST = 'http-method-list';
    const CONFIG_KEY_RETRY_ON_BAD_RESPONSE = 'retry-on-bad-response';
    const CONFIG_KEY_TOGGLE_URL_ENCODING = 'toggle-url-encoding';
    const CONFIG_KEY_REFERRER = 'referrer';
    const CONFIG_KEY_HTTP_CLIENT = 'http-client';

    /**
     * @var string[]
     */
    private $userAgents = array();

    /**
     * @var string[]
     */
    private $httpMethodList = array(
        self::HTTP_METHOD_HEAD,
        self::HTTP_METHOD_GET
    );

    /**
     * @var boolean
     */
    private $retryOnBadResponse = true;

    /**
     * @var boolean
     */
    private $toggleUrlEncoding = false;

    /**
     * @var string
     */
    private $referrer;

    /**
     * @var HttpClient
     */
    private $httpClient;


    /**
     * @param array $configurationValues
     */
    public function __construct($configurationValues)
    {
        if (isset($configurationValues[self::CONFIG_KEY_USER_AGENTS])) {
            $this->userAgents = $configurationValues[self::CONFIG_KEY_USER_AGENTS];
        }

        if (isset($configurationValues[self::CONFIG_KEY_HTTP_METHOD_LIST])) {
            $this->httpMethodList = $configurationValues[self::CONFIG_KEY_HTTP_METHOD_LIST];
        }

        if (isset($configurationValues[self::CONFIG_KEY_RETRY_ON_BAD_RESPONSE])) {
            $this->retryOnBadResponse = $configurationValues[self::CONFIG_KEY_RETRY_ON_BAD_RESPONSE];
        }

        if (isset($configurationValues[self::CONFIG_KEY_TOGGLE_URL_ENCODING])) {
            $this->toggleUrlEncoding = $configurationValues[self::CONFIG_KEY_TOGGLE_URL_ENCODING];
        }

        if (isset($configurationValues[self::CONFIG_KEY_REFERRER])) {
            $this->referrer = $configurationValues[self::CONFIG_KEY_REFERRER];
        }

        if (isset($configurationValues[self::CONFIG_KEY_HTTP_CLIENT])) {
            $this->httpClient = $configurationValues[self::CONFIG_KEY_HTTP_CLIENT];
        }
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }

        if (is_null($this->getHttpClientHistory())) {
            $this->httpClient->getEmitter()->attach(new HttpHistorySubscriber());
        }

        return $this->httpClient;
    }

    /**
     * @return boolean
     */
    public function getToggleUrlEncoding()
    {
        return $this->toggleUrlEncoding;
    }

    /**
     * @return boolean
     */
    public function getRetryOnBadResponse()
    {
        return $this->retryOnBadResponse;
    }

    /**
     * @return string[]
     */
    public function getHttpMethodList()
    {
        return $this->httpMethodList;
    }

    /**
     * @return string[]
     */
    public function getUserAgents()
    {
        return $this->userAgents;
    }

    /**
     * @return array
     */
    public function getUserAgentSelectionForRequest()
    {
        if (count($this->userAgents)) {
            return $this->userAgents;
        }

        return [
            $this->getHttpClient()->getDefaultUserAgent()
        ];
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @return bool
     */
    public function hasReferrer()
    {
        return trim($this->getReferrer()) != '';
    }

    /**
     * @return HttpHistorySubscriber
     */
    public function getHttpClientHistory()
    {
        $listenerCollections = $this->httpClient->getEmitter()->listeners('complete');

        foreach ($listenerCollections as $listener) {
            if ($listener[0] instanceof HttpHistorySubscriber) {
                return $listener[0];
            }
        }

        return null;
    }
}
