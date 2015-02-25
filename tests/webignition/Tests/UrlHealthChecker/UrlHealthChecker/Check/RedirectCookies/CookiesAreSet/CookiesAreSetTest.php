<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\RedirectCookies\CookiesAreSet;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\RedirectCookies\RedirectCookiesTest;

abstract class CookiesAreSetTest extends RedirectCookiesTest {

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