<?php

namespace webignition\Tests\UrlHealthChecker;

use webignition\UrlHealthChecker\UrlHealthChecker;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {


    /**
     * @var UrlHealthChecker
     */
    private $urlHealthChecker;


    public function __construct() {
        $this->urlHealthChecker = new UrlHealthChecker();
    }


    /**
     * @return UrlHealthChecker
     */
    protected function getUrlHealthChecker() {
        return $this->urlHealthChecker;
    }
    
}