<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

abstract class SimpleResponseTest extends CheckTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }

    protected function getHttpFixtures() {
        return [
            'HTTP/1.1 ' . $this->getExpectedResponseCode()
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'http';
    }

}