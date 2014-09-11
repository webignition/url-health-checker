<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

class WithDomainWithPathNoSecureTest extends CookiesAreSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/path';
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