<?php

namespace webignition\Tests\UrlHealthChecker\Configuration;

class BaseRequestTest extends ConfigurationTest {
    
    public function testGetDefaultBaseRequest() {
        $this->assertEquals($this->getHttpClient()->createRequest('GET'), $this->getConfiguration()->getBaseRequest());
    }
    
    public function testSetReturnsSelf() {
        $this->assertEquals(
            $this->getConfiguration(),
            $this->getConfiguration()->setBaseRequest($this->getHttpClient()->createRequest('GET'))
        );
    }

    public function testSetGetBaseRequest() {
        $username = 'example_user';
        $password = 'example_password';

        $baseRequest = $this->getHttpClient([
            'defaults' => [
                'auth'    => ['example_user', 'example_password'],
            ]
        ])->createRequest('GET');

        $this->getConfiguration()->setBaseRequest($baseRequest);

        $this->assertEquals([
            $username,
            $password
        ], $this->getConfiguration()->getBaseRequest()->getConfig()->get('auth'));

        $this->assertEquals($baseRequest->getConfig()->get('auth'), $this->getConfiguration()->getBaseRequest()->getConfig()->get('auth'));
    }
    
}