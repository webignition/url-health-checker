<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\Redirect;

class SuccessTest extends RedirectTest {

    protected function getHttpFixtures() {
        return [
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            'HTTP/1.1 200'
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'http';
    }

    protected function getExpectedResponseCode() {
        return 200;
    }
    
    
}