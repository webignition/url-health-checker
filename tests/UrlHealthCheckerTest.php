<?php

namespace webignition\Tests\UrlHealthChecker;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Message\ResponseInterface;
use GuzzleHttp\Subscriber\Mock as HttpMockSubscriber;
use webignition\UrlHealthChecker\Configuration;
use webignition\UrlHealthChecker\LinkState;
use webignition\UrlHealthChecker\UrlHealthChecker;

class UrlHealthCheckerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UrlHealthChecker
     */
    private $urlHealthChecker;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->urlHealthChecker = new UrlHealthChecker();
    }

    public function testGetConfiguration()
    {
        $this->assertInstanceOf(Configuration::class, $this->urlHealthChecker->getConfiguration());

        $newConfiguration = new Configuration([]);
        $this->urlHealthChecker->setConfiguration($newConfiguration);
        $this->assertEquals($newConfiguration, $this->urlHealthChecker->getConfiguration());
    }

    /**
     * @dataProvider checkDataProvider
     *
     * @param Configuration $configuration
     * @param array $httpFixtures
     * @param LinkState $expectedLinkState
     */
    public function testCheck(
        Configuration $configuration,
        $url,
        array $httpFixtures,
        LinkState $expectedLinkState
    ) {
        $httpClient = $configuration->getHttpClient();

        $httpClient->getEmitter()->attach(
            new HttpMockSubscriber($httpFixtures)
        );

        $this->urlHealthChecker->setConfiguration($configuration);
        $linkState = $this->urlHealthChecker->check($url);

        $this->assertEquals($expectedLinkState, $linkState);
    }

    /**
     * @return array
     */
    public function checkDataProvider()
    {
        /* @var RequestInterface $emptyRequest */
        $emptyRequest = \Mockery::mock(RequestInterface::class);

        $emptyResponse = \Mockery::mock(ResponseInterface::class);
        $emptyResponse
            ->shouldReceive('getStatusCode')
            ->andReturn(200);

        return [
            '200 OK' => [
                'configuration' => new Configuration([]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    'HTTP/1.1 200 OK',
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    200
                ),
            ],
            'too many redirects' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    "HTTP/1.1 301 Moved Permanently\nLocation: http://example.com/1",
                    "HTTP/1.1 301 Moved Permanently\nLocation: http://example.com/1",
                    "HTTP/1.1 301 Moved Permanently\nLocation: http://example.com/1",
                    "HTTP/1.1 301 Moved Permanently\nLocation: http://example.com/1",
                    "HTTP/1.1 301 Moved Permanently\nLocation: http://example.com/1",
                    "HTTP/1.1 301 Moved Permanently\nLocation: http://example.com/1",
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    301
                ),
            ],
            'http 404' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    'HTTP/1.1 404 Not Found',
                    'HTTP/1.1 404 Not Found',
                    'HTTP/1.1 404 Not Found',
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    404
                ),
            ],
            'http 404, retry on bad response=false' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                    Configuration::CONFIG_KEY_RETRY_ON_BAD_RESPONSE => false,
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    'HTTP/1.1 404 Not Found',
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    404
                ),
            ],
            'http 500' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    'HTTP/1.1 500 Internal Server Error',
                    'HTTP/1.1 500 Internal Server Error',
                    'HTTP/1.1 500 Internal Server Error',
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    500
                ),
            ],
            'curl 6' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    new ConnectException('cURL error 6: Unable to resolve host', $emptyRequest),
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    6
                ),
            ],
            'curl 28' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    new ConnectException('cURL error 28: Operation timed out', $emptyRequest),
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    28
                ),
            ],
            'malformed URL' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'host:65536',
                'httpFixtures' => [
                    new ConnectException('cURL error 28: Operation timed out', $emptyRequest),
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    3
                ),
            ],
            'curl 51 as RequestException' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    new RequestException(
                        'cURL error 51: SSL peer certificate or SSH remote key was not OK',
                        $emptyRequest
                    )
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    51
                ),
            ],
            'http 999' => [
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'httpFixtures' => [
                    'HTTP/1.1 999 Request denied',
                    'HTTP/1.1 999 Request denied',
                    'HTTP/1.1 999 Request denied',
                ],
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    999
                ),
            ],
        ];
    }
}
