<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreNotSet;

class NoDomainNoPathNoSecureTest extends CookiesAreNotSetTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }

    protected function getCookies() {
        return array(
            array(
                'name' => 'name1',
                'value' => 'value1'
            )                       
        );         
    }
    
}