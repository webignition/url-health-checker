<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\Retry;

class RetryAndFailTest extends RetryTest {

    protected function preCall() {
        $this->getUrlHealthChecker()->getConfiguration()->enableRetryOnBadResponse();
    }

    protected function getHttpFixtures() {
        return [
            'HTTP/1.1 404',
            'HTTP/1.1 404',
            'HTTP/1.1 404',
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'http';
    }

    protected function getExpectedResponseCode() {
        return 404;
    }
    
}