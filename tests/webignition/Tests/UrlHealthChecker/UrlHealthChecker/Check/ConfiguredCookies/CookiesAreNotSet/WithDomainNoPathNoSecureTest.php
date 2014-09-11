<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

class WithDomainNoPathNoSecureTest extends CookiesAreNotSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }
    
    protected function getCookies() {
        return array(
            array(
                'domain' => 'foo.example.com',
                'name' => 'name1',
                'value' => 'value1'
            )                       
        );         
    }
    
    
    
    
}