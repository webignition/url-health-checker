<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

class WithDomainWithPathNoSecureTest extends CookiesAreSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/path';
    }
    
    protected function getCookies() {
        return array(
            array(
                'Domain' => '.example.com',
                'Path' => '/path',
                'Name' => 'name1',
                'Value' => 'value1'
            )                       
        );         
    }
    
}