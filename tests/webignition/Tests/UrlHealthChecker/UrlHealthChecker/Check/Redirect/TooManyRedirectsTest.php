<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\Redirect;

class TooManyRedirectsTest extends RedirectTest {

    protected function getHttpFixtures() {
        return [
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n",
            "HTTP/1.1 301 Moved Permanently\r\nLocation: /redirect1\r\nContent-Length: 0\r\n\r\n"
        ];
    }

    protected function getExpectedLinkStateType() {
        return 'http';
    }

    protected function getExpectedResponseCode() {
        return 301;
    }
    
    
}