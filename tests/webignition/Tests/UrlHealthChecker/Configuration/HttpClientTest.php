<?php

namespace webignition\Tests\UrlHealthChecker\Configuration;

use GuzzleHttp\Client as HttpClient;

class HttpClientTest extends ConfigurationTest {
    
    public function testGetDefaultHttpClient() {
        $this->assertEquals(new HttpClient(), $this->getConfiguration()->getHttpClient());
    }
    
    public function testSetReturnsSelf() {
        $this->assertEquals(
            $this->getConfiguration(),
            $this->getConfiguration()->setHttpClient($this->getHttpClient())
        );
    }

    public function testSetGetHttpClient() {
        $httpClient = clone $this->getHttpClient([[
            'defaults' => [
                'auth'    => ['example_user', 'example_password'],
            ]
        ]]);

        $this->getConfiguration()->setHttpClient($httpClient);

        $this->assertEquals($httpClient->getDefaultOption('auth'), $this->getConfiguration()->getHttpClient()->getDefaultOption('auth'));
    }
    
}