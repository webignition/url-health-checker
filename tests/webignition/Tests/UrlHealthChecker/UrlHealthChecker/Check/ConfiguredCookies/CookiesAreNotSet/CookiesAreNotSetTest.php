<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\ConfiguredCookiesTest;

abstract class CookiesAreNotSetTest extends ConfiguredCookiesTest {

    /**
     *
     * @return \Guzzle\Http\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldBeSet() {
        return [];
    }


    /**
     *
     * @return \Guzzle\Http\Message\RequestInterface[]
     */
    protected function getExpectedRequestsOnWhichCookiesShouldNotBeSet() {
        $requests = [];

        foreach ($this->getHttpHistory()->getAll() as $httpTransaction) {
            $requests[] = $httpTransaction['request'];
        }

        return $requests;
    }

}