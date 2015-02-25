<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

class NoDomainNoPathNoSecureTest extends CookiesAreNotSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }

    protected function getCookies() {
        return array(
            array(
                'Name' => 'name1',
                'Value' => 'value1'
            )                       
        );         
    }
    
}