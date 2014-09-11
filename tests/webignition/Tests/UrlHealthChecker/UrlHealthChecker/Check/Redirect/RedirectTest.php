<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\Redirect;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\CheckTest;

abstract class RedirectTest extends CheckTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }
    
}