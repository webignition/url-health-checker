<?php

namespace webignition\Tests\UrlHealthChecker\Configuration;

use webignition\Tests\UrlHealthChecker\BaseTest;
use webignition\UrlHealthChecker\Configuration;

abstract class ConfigurationTest extends BaseTest {
    
    /**
     *
     * @var Configuration
     */
    private $configuration;
    
    
    public function setUp() {
        $this->configuration = new Configuration();
    }  
    
    
    /**
     * 
     * @return Configuration
     */
    protected function getConfiguration() {
        return $this->configuration;
    }
}