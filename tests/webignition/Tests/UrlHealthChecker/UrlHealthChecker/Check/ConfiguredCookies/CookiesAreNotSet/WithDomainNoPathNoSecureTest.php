<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

class WithDomainNoPathNoSecureTest extends CookiesAreNotSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }
    
    protected function getCookies() {
        return array(
            array(
                'Domain' => 'foo.example.com',
                'Name' => 'name1',
                'Value' => 'value1'
            )                       
        );         
    }
    
    
    
    
}