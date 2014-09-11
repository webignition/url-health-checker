<?php

namespace webignition\Tests\UrlHealthChecker\Configuration\GetUserAgentSelectionForRequest;

use webignition\Tests\UrlHealthChecker\Configuration\ConfigurationTest;

class DefaultSelectionTest extends ConfigurationTest {
    
    public function testDefaultSelectionHasOne() {                
        $this->assertEquals(1, count($this->getConfiguration()->getUserAgentSelectionForRequest()));
    }   
    
    
    public function testDefaultSelectionIsHttpClientLibraryAgent() {      
        $defaultSelection = $this->getConfiguration()->getUserAgentSelectionForRequest();
        $defaultUserAgent = $defaultSelection[0];
        
        $agentNames = array(
            'Guzzle',
            'curl',
            'PHP'
        );
        
        foreach ($agentNames as $agentName) {
            $this->assertTrue(preg_match('/'.$agentName.'\/[0-9]+\.[0-9]+\.[0-9]+/', $defaultUserAgent) === 1);
        }
    }
    
}