<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

class WithDomainWithPathNoSecureTest extends CookiesAreNotSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }
    
    protected function getCookies() {
        return array(
            array(
                'domain' => '.example.com',
                'path' => '/path',
                'name' => 'name1',
                'value' => 'value1'
            )                       
        );         
    }
    
}