<?php

namespace webignition\Tests\UrlHealthChecker;

use GuzzleHttp\Message\RequestInterface;
use webignition\UrlHealthChecker\Configuration;
use webignition\UrlHealthChecker\RequestSetFactory;

class RequestSetFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param Configuration $configuration
     * @param string $url
     * @param int $expectedRequestCount
     * @param array $expectedRequestDataCollection
     */
    public function testCreate(
        Configuration $configuration,
        $url,
        $expectedRequestCount,
        $expectedRequestDataCollection
    ) {
        $requestSetFactory = new RequestSetFactory();
        $requestSetFactory->setConfiguration($configuration);

        $requests = $requestSetFactory->create($url);

        $this->assertCount($expectedRequestCount, $requests);

        foreach ($requests as $requestIndex => $request) {
            /* @var RequestInterface $request */
            $expectedRequestData = $expectedRequestDataCollection[$requestIndex];

            $this->assertEquals($expectedRequestData['method'], $request->getMethod());
            $this->assertEquals($expectedRequestData['url'], $request->getUrl());
            $this->assertRegExp($expectedRequestData['user-agent'], $request->getHeader('user-agent'));
            $this->assertEquals($expectedRequestData['referrer'], $request->getHeader('referer'));
        }
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            'default' => [
                'configuration' => new Configuration([]),
                'url' => 'http://example.com/',
                'expectedRequestCount' => 2,
                'expectedRequestDataCollection' => [
                    [
                        'method' => Configuration::HTTP_METHOD_HEAD,
                        'url' => 'http://example.com/',
                        'user-agent' => '/^Guzzle\/5.* curl\/.*/',
                        'referrer' => '',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_GET,
                        'url' => 'http://example.com/',
                        'user-agent' => '/^Guzzle\/5.* curl\/.*/',
                        'referrer' => '',
                    ],
                ],
            ],
            'non-default' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_USER_AGENTS => [
                        'foo',
                        'bar',
                    ],
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_HEAD,
                        Configuration::HTTP_METHOD_GET,
                    ],
                    Configuration::CONFIG_KEY_TOGGLE_URL_ENCODING => true,
                    Configuration::CONFIG_KEY_REFERRER => 'referrer',
                ]),
                'url' => 'http://example.com/?foo bar',
                'expectedRequestCount' => 8,
                'expectedRequestDataCollection' => [
                    [
                        'method' => Configuration::HTTP_METHOD_HEAD,
                        'url' => 'http://example.com/?foo%20bar',
                        'user-agent' => '/^foo$/',
                        'referrer' => 'referrer',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_HEAD,
                        'url' => 'http://example.com/?foo bar',
                        'user-agent' => '/^foo$/',
                        'referrer' => 'referrer',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_GET,
                        'url' => 'http://example.com/?foo%20bar',
                        'user-agent' => '/^foo$/',
                        'referrer' => 'referrer',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_GET,
                        'url' => 'http://example.com/?foo bar',
                        'user-agent' => '/^foo$/',
                        'referrer' => 'referrer',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_HEAD,
                        'url' => 'http://example.com/?foo%20bar',
                        'user-agent' => '/^bar$/',
                        'referrer' => 'referrer',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_HEAD,
                        'url' => 'http://example.com/?foo bar',
                        'user-agent' => '/^bar$/',
                        'referrer' => 'referrer',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_GET,
                        'url' => 'http://example.com/?foo%20bar',
                        'user-agent' => '/^bar$/',
                        'referrer' => 'referrer',
                    ],
                    [
                        'method' => Configuration::HTTP_METHOD_GET,
                        'url' => 'http://example.com/?foo bar',
                        'user-agent' => '/^bar$/',
                        'referrer' => 'referrer',
                    ],
                ],
            ],
        ];
    }
}
