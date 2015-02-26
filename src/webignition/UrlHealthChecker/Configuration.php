<?php

namespace webignition\UrlHealthChecker;

use GuzzleHttp\Message\RequestInterface as HttpRequest;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\History as HttpHistorySubscriber;

class Configuration {

    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_GET = 'GET';
    
    const CURL_MALFORMED_URL_CODE = 3;
    const CURL_MALFORMED_URL_MESSAGE = 'The URL was not properly formatted.';   

    /**
     *
     * @var array
     */
    private $userAgents = array();
    
    
    /**
     *
     * @var array
     */
    private $httpMethodList = array(
        self::HTTP_METHOD_HEAD,
        self::HTTP_METHOD_GET
    );

    
//    /**
//     *
//     * @var HttpRequest
//     */
//    private $baseRequest = null;

    
    /**
     *
     * @var boolean
     */
    private $retryOnBadResponse = true;
    
    
    /**
     *
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
     * @param HttpClient $httpClient
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient) {
        $this->httpClient = $httpClient;
        return $this;
    }


    /**
     * @return HttpClient
     */
    public function getHttpClient() {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient();
        }

        return $this->httpClient;
    }
    
    
    /**
     * 
     * @return Configuration
     */
    public function enableToggleUrlEncoding() {
        $this->toggleUrlEncoding = true;
        return $this;
    }
    
    
    /**
     * 
     * @return Configuration
     */
    public function disableToggleUrlEncoding() {
        $this->toggleUrlEncoding = false;
        return $this;
    }    
    
    
    /**
     * 
     * @return boolean
     */
    public function getToggleUrlEncoding() {
        return $this->toggleUrlEncoding;
    }
    
    
    /**
     * 
     * @return Configuration
     */
    public function enableRetryOnBadResponse() {
        $this->retryOnBadResponse = true;
        return $this;
    }
    
    
    /**
     * 
     * @return Configuration
     */
    public function disableRetryOnBadResponse() {
        $this->retryOnBadResponse = false;
        return $this;
    }    
    
    
    /**
     * 
     * @return boolean
     */
    public function getRetryOnBadResponse() {
        return $this->retryOnBadResponse;
    }
    
    
    /**
     * 
     * @param string[] $httpMethodList
     * @return Configuration
     */
    public function setHttpMethodList($httpMethodList) {
        $this->httpMethodList = $httpMethodList;
        return $this;
    }
    
    
    /**
     * 
     * @return string[]
     */
    public function getHttpMethodList() {
        return $this->httpMethodList;
    }
    
    
    /**
     * 
     * @param string[] $userAgents
     * @return Configuration
     */
    public function setUserAgents($userAgents) {
        $this->userAgents = $userAgents;
        return $this;
    }
    
    
    /**
     * 
     * @return string[]
     */
    public function getUserAgents() {
        return $this->userAgents;
    }

    
    /**
     * 
     * @return array
     */
    public function getUserAgentSelectionForRequest() {
        if (count($this->userAgents)) {
            return $this->userAgents;
        }

        return [
            $this->getHttpClient()->getDefaultUserAgent()
        ];
    }


    /**
     * @param string $referrer
     * @return Configuration
     */
    public function setReferrer($referrer) {
        $this->referrer = $referrer;
        return $this;
    }


    /**
     * @return string
     */
    public function getReferrer() {
        return $this->referrer;
    }


    /**
     * @return bool
     */
    public function hasReferrer() {
        return trim($this->getReferrer()) != '';
    }
    
}