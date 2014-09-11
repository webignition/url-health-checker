<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\Retry;

class RetryDisabledTest extends RetryTest {

    protected function preCall() {
        $this->getUrlHealthChecker()->getConfiguration()->disableRetryOnBadResponse();
    }

    protected function getHttpFixtures() {
        return [
            'HTTP/1.1 500',
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'http';
    }

    protected function getExpectedResponseCode() {
        return 500;
    }
    
}