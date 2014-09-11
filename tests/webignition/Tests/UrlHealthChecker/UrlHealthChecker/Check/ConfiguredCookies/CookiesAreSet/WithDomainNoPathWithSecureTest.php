<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

class WithDomainNoPathWithSecureTest extends CookiesAreSetTest {

    protected function getRequestUrl() {
        return 'https://example.com/';
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