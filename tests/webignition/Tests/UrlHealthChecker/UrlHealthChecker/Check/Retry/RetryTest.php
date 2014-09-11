<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\Retry;

use webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check\CheckTest;

abstract class RetryTest extends CheckTest {

    protected function getRequestUrl() {
        return 'http://example.com/';
    }

}