<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

class Status500Test extends SimpleResponseTest {

    protected function getExpectedResponseCode() {
        return 500;
    }
    
    
}