<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\ConfiguredCookiesTest;

abstract class CookiesAreSetTest extends ConfiguredCookiesTest {

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