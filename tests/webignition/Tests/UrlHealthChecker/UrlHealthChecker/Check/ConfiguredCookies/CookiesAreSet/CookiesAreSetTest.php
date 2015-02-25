<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\ConfiguredCookiesTest;

abstract class CookiesAreSetTest extends ConfiguredCookiesTest {

    /**
     *
     * @return \GuzzleHttp\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldBeSet() {
        return $this->getHttpHistory()->getRequests();
    }


    /**
     *
     * @return \GuzzleHttp\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return [];
    }

}