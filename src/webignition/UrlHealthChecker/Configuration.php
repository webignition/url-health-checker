<?php

namespace webignition\UrlHealthChecker;

use Guzzle\Http\Message\RequestInterface as HttpRequest;
use Guzzle\Http\Client as HttpClient;
use Guzzle\Plugin\History\HistoryPlugin as HttpHistoryPlugin;

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

    
    /**
     *
     * @var \Guzzle\Http\Message\Request
     */
    private $baseRequest = null;

    
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
     *
     * @var array
     */
    private $cookies = array();


    /**
     * @var bool
     */
    private $preserveCookiesOnRedirect = false;


    /**
     * @var string
     */
    private $referrer;
    
    
    /**
     * 
     * @param HttpRequest $request
     * @return Configuration
     */
    public function setBaseRequest(HttpRequest $request) {
        $this->baseRequest = $request;
        return $this;
    }
    
    
    
    /**
     * 
     * @return HttpRequest $request
     */
    public function getBaseRequest() {
        if (is_null($this->baseRequest)) {
            $client = new HttpClient;
            $client->addSubscriber(new HttpHistoryPlugin());
            $this->baseRequest = $client->get();
        }
        
        return $this->baseRequest;
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
        
        return $this->getBaseRequest()->getClient()->get()->getHeader('User-Agent')->toArray();     
    }

    
    /**
     * 
     * @param array $cookies
     * @return Configuration
     */
    public function setCookies($cookies) {
        $this->cookies = $cookies;
        return $this;
    }
    
    
    /**
     * 
     * @return array
     */
    public function getCookies() {
        return $this->cookies;
    }


    /**
     * @return Configuration
     */
    public function enablePreserveCookiesOnRedirect() {
        $this->preserveCookiesOnRedirect = true;
        return $this;
    }


    /**
     * @return Configuration
     */
    public function disablePreserveCookiesOnRedirect() {
        $this->preserveCookiesOnRedirect = false;
        return $this;
    }


    /**
     * @return bool
     */
    public function preserveCookiesOnRedirect() {
        return $this->preserveCookiesOnRedirect;
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