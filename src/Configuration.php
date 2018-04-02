<?php

namespace webignition\UrlHealthChecker;

class Configuration
{
    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_GET = 'GET';

    const CURL_MALFORMED_URL_CODE = 3;
    const CURL_MALFORMED_URL_MESSAGE = 'The URL was not properly formatted.';

    const CONFIG_KEY_USER_AGENTS = 'user-agents';
    const CONFIG_KEY_HTTP_METHOD_LIST = 'http-method-list';
    const CONFIG_KEY_RETRY_ON_BAD_RESPONSE = 'retry-on-bad-response';
    const CONFIG_KEY_REFERRER = 'referrer';

    /**
     * @var string[]
     */
    private $userAgents = [];

    /**
     * @var string[]
     */
    private $httpMethodList = [
        self::HTTP_METHOD_HEAD,
        self::HTTP_METHOD_GET
    ];

    /**
     * @var bool
     */
    private $retryOnBadResponse = true;

    /**
     * @var string
     */
    private $referrer;

    /**
     * @param array $configurationValues
     */
    public function __construct(array $configurationValues = [])
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

        if (isset($configurationValues[self::CONFIG_KEY_REFERRER])) {
            $this->referrer = $configurationValues[self::CONFIG_KEY_REFERRER];
        }
    }

    /**
     * @return bool
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
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }
}
