<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

class CycleUserAgentsTest extends CheckTest {

    protected function preCall() {
        $this->getUrlHealthChecker()->getConfiguration()->setUserAgents(array('foo', 'bar'));
    }

    protected function getRequestUrl() {
        return 'http://example.com/';
    }

    protected function getHttpFixtures() {
        return [
            'HTTP/1.1 404 Not Found',
            'HTTP/1.1 200 Ok'
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'http';
    }

    protected function getExpectedResponseCode() {
        return 200;
    }

}