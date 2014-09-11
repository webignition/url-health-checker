<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\CheckTest;

abstract class ConfiguredCookiesTest extends CheckTest {

    /**
     * 
     * @return array
     */
    abstract protected function getCookies();
    
    /**
     * 
     * @return \Guzzle\Http\Message\RequestInterface[]
     */    
    abstract protected function getExpectedRequestsOnWhichCookiesShouldBeSet();
    
    
    /**
     * 
     * @return \Guzzle\Http\Message\RequestInterface[]
     */    
    abstract protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet();


    protected function preCall() {
        $this->getUrlHealthChecker()->getConfiguration()->setCookies($this->getCookies());
    }


    protected function getHttpFixtures() {
        return [
            'HTTP/1.1 200'
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'http';
    }

    protected function getExpectedResponseCode() {
        return 200;
    }

    
    public function testCookiesAreSetOnExpectedRequests() {
        foreach ($this->getExpectedRequestsOnWhichCookiesShouldBeSet() as $request) {
            $this->assertEquals($this->getExpectedCookieValues(), $request->getCookies());
        }
    }


    public function testCookiesAreNotSetOnExpectedRequests() {
        foreach ($this->getExpectedRequestsOnWhichCookiesShouldNotBeSet() as $request) {
            $this->assertEquals(array(), $request->getCookies());
        }
    }
    

    /**
     * 
     * @return array
     */
    protected function getExpectedCookieValues() {
        $nameValueArray = array();
        
        foreach ($this->getCookies() as $cookie) {
            $nameValueArray[$cookie['name']] = $cookie['value'];
        }
        
        return $nameValueArray;
    }
    
}