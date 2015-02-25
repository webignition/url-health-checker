<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\ConfiguredCookiesTest;

abstract class CookiesAreNotSetTest extends ConfiguredCookiesTest {

    /**
     *
     * @return \GuzzleHttp\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldBeSet() {
        return [];
    }


    /**
     *
     * @return \GuzzleHttp\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return $this->getHttpHistory()->getRequests();
    }

}