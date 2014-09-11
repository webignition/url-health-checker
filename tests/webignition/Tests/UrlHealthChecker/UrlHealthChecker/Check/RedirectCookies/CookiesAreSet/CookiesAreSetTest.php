<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\RedirectCookies\CookiesAreSet;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\RedirectCookies\RedirectCookiesTest;

abstract class CookiesAreSetTest extends RedirectCookiesTest {

    /**
     *
     * @return \Guzzle\Http\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldBeSet() {
        $requests = [];

        foreach ($this->getHttpHistory()->getAll() as $httpTransaction) {
            $requests[] = $httpTransaction['request'];
        }

        return $requests;
    }


    /**
     *
     * @return \Guzzle\Http\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        return [];
    }

}