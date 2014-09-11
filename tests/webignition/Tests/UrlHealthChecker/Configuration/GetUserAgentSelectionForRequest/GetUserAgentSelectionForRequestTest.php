<?php

namespace webignition\Tests\UrlHealthChecker\Configuration\GetUserAgentSelectionForRequest;

use webignition\Tests\UrlHealthChecker\Configuration\ConfigurationTest;

class GetUserAgentSelectionForRequestTest extends ConfigurationTest {
    
    public function testGetReturnsSelectionSet() {        
        $userAgents = array(
            'foo',
            'bar'
        );
        
        $this->getConfiguration()->setUserAgents($userAgents);
        $this->assertEquals($userAgents, $this->getConfiguration()->getUserAgentSelectionForRequest());
    }
    
}