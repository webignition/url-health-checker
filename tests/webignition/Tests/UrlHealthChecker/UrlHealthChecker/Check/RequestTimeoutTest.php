<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Message\Request;

class RequestTimeoutTest extends CheckTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }

    protected function getHttpFixtures() {
        return [
            new ConnectException(
                'cURL error 28: Operation timeout. The specified time-out period was reached according to the conditions.',
                new Request('GET', 'http://example.com/')
            ),
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'curl';
    }

    protected function getExpectedResponseCode() {
        return 28;
    }
    
}