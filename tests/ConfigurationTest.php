<?php

namespace webignition\Tests\UrlHealthChecker;

use webignition\UrlHealthChecker\Configuration;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array $configurationValues
     * @param string[] $expectedHttpMethodList
     * @param string $expectedReferrer
     * @param bool $expectedRetryOnBadResponse
     * @param string[] $expectedUserAgents
     */
    public function testCreate(
        array $configurationValues,
        array $expectedHttpMethodList,
        string $expectedReferrer,
        bool $expectedRetryOnBadResponse,
        array $expectedUserAgents
    ) {
        $configuration = new Configuration($configurationValues);

        $this->assertEquals($expectedHttpMethodList, $configuration->getHttpMethodList());
        $this->assertEquals($expectedReferrer, $configuration->getReferrer());
        $this->assertEquals($expectedRetryOnBadResponse, $configuration->getRetryOnBadResponse());
        $this->assertEquals($expectedUserAgents, $configuration->getUserAgents());
    }

    public function createDataProvider(): array
    {
        return [
            'default' => [
                'configurationValues' => [],
                'expectedHttpMethodList' => [
                    Configuration::HTTP_METHOD_HEAD,
                    Configuration::HTTP_METHOD_GET,
                ],
                'expectedReferrer' => '',
                'expectedRetryOnBadResponse' => true,
                'expectedUserAgents' => [],
            ],
            'non-default' => [
                'configurationValues' => [
                    Configuration::CONFIG_KEY_USER_AGENTS => [
                        'foo',
                        'bar',
                    ],
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                    Configuration::CONFIG_KEY_RETRY_ON_BAD_RESPONSE => false,
                    Configuration::CONFIG_KEY_REFERRER => 'referrer',
                ],
                'expectedHttpMethodList' => [
                    Configuration::HTTP_METHOD_GET,
                ],
                'expectedReferrer' => 'referrer',
                'expectedRetryOnBadResponse' => false,
                'expectedUserAgents' => [
                    'foo',
                    'bar',
                ],
            ],
        ];
    }
}
