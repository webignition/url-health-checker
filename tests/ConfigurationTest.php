<?php

namespace webignition\Tests\UrlHealthChecker;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\History as HttpHistorySubscriber;
use webignition\UrlHealthChecker\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array $configurationValues
     * @param string[] $expectedHttpMethodList
     * @param string $expectedReferrer
     * @param bool $expectedRetryOnBadResponse
     * @param bool $expectedToggleUrlEncoding
     * @param string[] $expectedUserAgents
     * @param string[] $expectedUserAgentSelectionForRequest
     * @param bool $expectedHasReferrer
     */
    public function testCreate(
        $configurationValues,
        $expectedHttpMethodList,
        $expectedReferrer,
        $expectedRetryOnBadResponse,
        $expectedToggleUrlEncoding,
        $expectedUserAgents,
        $expectedUserAgentSelectionForRequest,
        $expectedHasReferrer
    ) {
        $configuration = new Configuration($configurationValues);

        $this->assertEquals($expectedHttpMethodList, $configuration->getHttpMethodList());
        $this->assertEquals($expectedReferrer, $configuration->getReferrer());
        $this->assertEquals($expectedRetryOnBadResponse, $configuration->getRetryOnBadResponse());
        $this->assertEquals($expectedToggleUrlEncoding, $configuration->getToggleUrlEncoding());
        $this->assertEquals($expectedUserAgents, $configuration->getUserAgents());
        $this->assertEquals($expectedHasReferrer, $configuration->hasReferrer());
        $this->assertInstanceOf(HttpClient::class, $configuration->getHttpClient());
        $this->assertInstanceOf(HttpHistorySubscriber::class, $configuration->getHttpClientHistory());

        $userAgentSelectionForRequest = $configuration->getUserAgentSelectionForRequest();
        $this->assertCount(count($expectedUserAgentSelectionForRequest), $userAgentSelectionForRequest);

        foreach ($expectedUserAgentSelectionForRequest as $userAgentIndex => $expectedUserAgent) {
            $userAgent = $userAgentSelectionForRequest[$userAgentIndex];
            $this->assertRegExp($expectedUserAgent, $userAgent);
        }
    }

    /**
     * @return array
     */
    public function createDataProvider()
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
                'expectedToggleUrlEncoding' => false,
                'expectedUserAgents' => [],
                'expectedUserAgentSelectionForRequest' => [
                    '/^Guzzle\/5.* curl\/.*/',
                ],
                'expectedHasReferrer' => false,
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
                    Configuration::CONFIG_KEY_TOGGLE_URL_ENCODING => true,
                    Configuration::CONFIG_KEY_REFERRER => 'referrer',
                    Configuration::CONFIG_KEY_HTTP_CLIENT => new HttpClient(),
                ],
                'expectedHttpMethodList' => [
                    Configuration::HTTP_METHOD_GET,
                ],
                'expectedReferrer' => 'referrer',
                'expectedRetryOnBadResponse' => false,
                'expectedToggleUrlEncoding' => true,
                'expectedUserAgents' => [
                    'foo',
                    'bar',
                ],
                'expectedUserAgentSelectionForRequest' => [
                    '/^foo$/',
                    '/^bar$/',
                ],
                'expectedHasReferrer' => true,
            ],
        ];
    }
}
