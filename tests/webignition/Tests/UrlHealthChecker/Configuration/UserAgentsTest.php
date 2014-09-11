<?php

namespace webignition\Tests\UrlHealthChecker\Configuration;

class UserAgentsTest extends ConfigurationTest {
    
    public function testGetDefault() {        
        $this->assertEquals(array(), $this->getConfiguration()->getUserAgents());
    }
    
    public function testSetReturnsSelf() {
        $this->assertEquals($this->getConfiguration(), $this->getConfiguration()->setUserAgents(array()));
    }
    
    public function testSetGetUserAgents() {     
        $userAgents = array(
            'foo',
            'bar'
        );
        
        $this->assertEquals($userAgents, $this->getConfiguration()->setUserAgents($userAgents)->getUserAgents());
    }
    
}