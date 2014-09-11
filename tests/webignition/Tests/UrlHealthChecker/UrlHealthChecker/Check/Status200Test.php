<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

class Status200Test extends SimpleResponseTest {

    protected function getExpectedResponseCode() {
        return 200;
    }
    
}