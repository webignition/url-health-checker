<?php

namespace webignition\Tests\UrlHealthChecker\Configuration;

class RetryOnBadResponseTest extends ConfigurationTest {
    
    public function testGetDefault() {        
        $this->assertTrue($this->getConfiguration()->getRetryOnBadResponse());
    }
    
    public function testEnableReturnsSelf() {
        $this->assertEquals($this->getConfiguration(), $this->getConfiguration()->enableRetryOnBadResponse());
    }
    
    
    public function testDisableReturnsSelf() {
        $this->assertEquals($this->getConfiguration(), $this->getConfiguration()->disableRetryOnBadResponse());
    }    
    
    public function testEnableGetsTrue() {             
        $this->assertTrue($this->getConfiguration()->enableRetryOnBadResponse()->getRetryOnBadResponse());
    }
    
    public function testDisableGetsFalse() {             
        $this->assertFalse($this->getConfiguration()->disableRetryOnBadResponse()->getRetryOnBadResponse());
    }    
    
}