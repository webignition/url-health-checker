<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\RedirectCookies\CookiesAreSet;

class RedirectTest extends CookiesAreSetTest {

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

    protected function getRequestUrl() {
        return 'http://example.com/';
    }
    
    protected function getCookies() {
        return array(
            array(
                'Domain' => '.example.com',
                'Name' => 'name1',
                'Value' => 'value1'
            )                       
        );         
    }
    
    
    
    
}