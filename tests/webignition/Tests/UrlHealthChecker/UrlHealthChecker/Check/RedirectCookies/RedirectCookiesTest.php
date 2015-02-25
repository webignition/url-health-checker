<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\RedirectCookies;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\CheckTest;
use GuzzleHttp\Subscriber\Cookie as HttpCookieSubscriber;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Message\RequestInterface as HttpRequest;

abstract class RedirectCookiesTest extends CheckTest {

    /**
     * 
     * @return array
     */
    abstract protected function getCookies();
    
    /**
     * 
     * @return \GuzzleHttp\Message\RequestInterface[]
     */    
    abstract protected function getExpectedRequestsOnWhichCookiesShouldBeSet();
    
    
    /**
     * 
     * @return \GuzzleHttp\Message\RequestInterface[]
     */    
    abstract protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet();

    protected function preConstructHealthChecker() {
        $cookieJar = new CookieJar();

        foreach ($this->getCookies() as $cookieData) {
            $cookieJar->setCookie(new SetCookie($cookieData));
        }

        $this->getHttpClient()->getEmitter()->attach(new HttpCookieSubscriber($cookieJar));
    }


    protected function getExpectedLinkStateType() {
        return 'http';
    }

    protected function getExpectedResponseCode() {
        return 200;
    }

    
    public function testCookiesAreSetOnExpectedRequests() {
        foreach ($this->getExpectedRequestsOnWhichCookiesShouldBeSet() as $request) {
            $this->assertEquals($this->getExpectedCookieValues(), $this->getRequestCookieValues($request));
        }
    }


    public function testCookiesAreNotSetOnExpectedRequests() {
        foreach ($this->getExpectedRequestsOnWhichCookiesShouldNotBeSet() as $request) {
            $this->assertEquals(array(), $this->getRequestCookieValues($request));
        }
    }
    

    /**
     * 
     * @return array
     */
    protected function getExpectedCookieValues() {
        $nameValueArray = array();
        
        foreach ($this->getCookies() as $cookie) {
            $nameValueArray[$cookie['Name']] = $cookie['Value'];
        }
        
        return $nameValueArray;
    }


    private function getRequestCookieValues(HttpRequest $request) {
        if (!$request->hasHeader('Cookie')) {
            return [];
        }

        $cookieStrings = explode(';', $request->getHeader('Cookie'));
        $values = [];

        foreach ($cookieStrings as $cookieString) {
            $cookieString = trim($cookieString);
            $currentValues = explode('=', $cookieString);
            $values[$currentValues[0]] = $currentValues[1];
        }

        return $values;
    }
    
}