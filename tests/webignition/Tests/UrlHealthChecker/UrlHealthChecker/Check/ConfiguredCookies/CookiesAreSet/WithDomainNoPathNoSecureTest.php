<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

class WithDomainNoPathNoSecureTest extends CookiesAreSetTest {

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