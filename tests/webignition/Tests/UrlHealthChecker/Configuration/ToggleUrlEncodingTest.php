<?php

namespace webignition\Tests\UrlHealthChecker\Configuration;

class ToggleUrlEncodingTest extends ConfigurationTest {
    
    public function testGetDefault() {        
        $this->assertFalse($this->getConfiguration()->getToggleUrlEncoding());
    }
    
    public function testEnableReturnsSelf() {
        $this->assertEquals($this->getConfiguration(), $this->getConfiguration()->enableToggleUrlEncoding());
    }
    
    
    public function testDisableReturnsSelf() {
        $this->assertEquals($this->getConfiguration(), $this->getConfiguration()->disableToggleUrlEncoding());
    }    
    
    public function testEnableGetsTrue() {             
        $this->assertTrue($this->getConfiguration()->enableToggleUrlEncoding()->getToggleUrlEncoding());
    }
    
    public function testDisableGetsFalse() {             
        $this->assertFalse($this->getConfiguration()->disableToggleUrlEncoding()->getToggleUrlEncoding());
    }    
    
}