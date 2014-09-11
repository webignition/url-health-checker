<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\RedirectCookies;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\CheckTest;
use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Guzzle\Plugin\Cookie\Cookie;

abstract class RedirectCookiesTest extends CheckTest {

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

    protected function preConstructHealthChecker() {
        $cookieJar = new ArrayCookieJar();

        foreach ($this->getCookies() as $cookieData) {
            $cookieJar->add(new Cookie($cookieData));
        }

        $cookiePlugin = new CookiePlugin($cookieJar);

        $this->getHttpClient()->addSubscriber($cookiePlugin);
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