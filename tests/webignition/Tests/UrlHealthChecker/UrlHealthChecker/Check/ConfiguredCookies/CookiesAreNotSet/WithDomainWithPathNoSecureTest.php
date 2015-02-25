<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

class WithDomainWithPathNoSecureTest extends CookiesAreNotSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
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