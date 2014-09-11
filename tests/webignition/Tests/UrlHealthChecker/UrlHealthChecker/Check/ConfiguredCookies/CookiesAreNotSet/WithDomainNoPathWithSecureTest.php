<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

class WithDomainNoPathWithSecureTest extends CookiesAreNotSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }
    
    protected function getCookies() {
        return array(
            array(
                'domain' => '.example.com',
                'secure' => true,
                'name' => 'name1',
                'value' => 'value1'
            )                       
        );         
    }

    
}