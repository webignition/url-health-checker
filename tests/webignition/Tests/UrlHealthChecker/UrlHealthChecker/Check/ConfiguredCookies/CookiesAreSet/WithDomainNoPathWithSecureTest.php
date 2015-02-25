<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\ConfiguredCookies\CookiesAreSet;

class WithDomainNoPathWithSecureTest extends CookiesAreSetTest {

    protected function getRequestUrl() {
        return 'https://example.com/';
    }
    
    protected function getCookies() {
        return array(
            array(
                'Domain' => '.example.com',
                'Secure' => true,
                'Name' => 'name1',
                'Value' => 'value1'
            )                       
        );         
    }

}