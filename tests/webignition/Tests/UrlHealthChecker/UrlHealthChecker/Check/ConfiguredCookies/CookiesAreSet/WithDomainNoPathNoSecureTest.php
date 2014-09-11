<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

class WithDomainNoPathNoSecureTest extends CookiesAreSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }
    
    protected function getCookies() {
        return array(
            array(
                'domain' => '.example.com',
                'name' => 'name1',
                'value' => 'value1'
            )                       
        );         
    }
    
    
    
    
}