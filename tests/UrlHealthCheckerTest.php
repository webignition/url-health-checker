<?php

namespace webignition\Tests\UrlHealthChecker;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use webignition\HttpHistoryContainer\Container as HttpHistoryContainer;
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
     * @var MockHandler
     */
    private $mockHandler;

    /**
     * @var HttpHistoryContainer
     */
    private $httpHistory;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);

        $this->httpHistory = new HttpHistoryContainer();
        $handlerStack->push(Middleware::history($this->httpHistory));

        $httpClient = new HttpClient(['handler' => $handlerStack]);

        $this->urlHealthChecker = new UrlHealthChecker();
        $this->urlHealthChecker->setHttpClient($httpClient);
    }

    public function testGetConfiguration()
    {
        $this->assertInstanceOf(Configuration::class, $this->urlHealthChecker->getConfiguration());

        $newConfiguration = new Configuration();
        $this->urlHealthChecker->setConfiguration($newConfiguration);
        $this->assertEquals($newConfiguration, $this->urlHealthChecker->getConfiguration());
    }

    public function testSetHttpClient()
    {
        $this->urlHealthChecker->setHttpClient(new HttpClient());
    }

    /**
     * @dataProvider checkDataProvider
     *
     * @param array $httpFixtures
     * @param Configuration $configuration
     * @param string $url
     * @param LinkState $expectedLinkState
     *
     * @throws GuzzleException
     */
    public function testCheck(
        array $httpFixtures,
        Configuration $configuration,
        $url,
        LinkState $expectedLinkState
    ) {
        $this->appendHttpFixtures($httpFixtures);

        $this->urlHealthChecker->setConfiguration($configuration);
        $linkState = $this->urlHealthChecker->check($url);

        $this->assertEquals($expectedLinkState, $linkState);
    }

    /**
     * @return array
     */
    public function checkDataProvider()
    {
        $emptyRequest = new Request('GET', 'http://example.com');

        $redirectResponse = new Response(301, ['Location' => 'http://example.com/1']);
        $notFoundResponse = new Response(404);
        $internalServerErrorResponse = new Response(500);
        $requestDeniedResponse = new Response(999);

        return [
            '200 OK' => [
                'httpFixtures' => [
                    new Response(),
                ],
                'configuration' => new Configuration([]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    200
                ),
            ],
            'too many redirects' => [
                'httpFixtures' => [
                    $redirectResponse,
                    $redirectResponse,
                    $redirectResponse,
                    $redirectResponse,
                    $redirectResponse,
                    $redirectResponse,
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    301
                ),
            ],
            'http 404' => [
                'httpFixtures' => [
                    $notFoundResponse,
                    $notFoundResponse,
                    $notFoundResponse,
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    404
                ),
            ],
            'http 404, retry on bad response=false' => [
                'httpFixtures' => [
                    $notFoundResponse,
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                    Configuration::CONFIG_KEY_RETRY_ON_BAD_RESPONSE => false,
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    404
                ),
            ],
            'http 500' => [
                'httpFixtures' => [
                    $internalServerErrorResponse,
                    $internalServerErrorResponse,
                    $internalServerErrorResponse,
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    500
                ),
            ],
            'curl 6' => [
                'httpFixtures' => [
                    new ConnectException('cURL error 6: Unable to resolve host', $emptyRequest),
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    6
                ),
            ],
            'curl 28' => [
                'httpFixtures' => [
                    new ConnectException('cURL error 28: Operation timed out', $emptyRequest),
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    28
                ),
            ],
            'malformed URL' => [
                'httpFixtures' => [],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'host:65536',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    3
                ),
            ],
            'curl 51 as RequestException' => [
                'httpFixtures' => [
                    new RequestException(
                        'cURL error 51: SSL peer certificate or SSH remote key was not OK',
                        $emptyRequest
                    ),
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_CURL,
                    51
                ),
            ],
            'http 999' => [
                'httpFixtures' => [
                    $requestDeniedResponse,
                    $requestDeniedResponse,
                    $requestDeniedResponse,
                ],
                'configuration' => new Configuration([
                    Configuration::CONFIG_KEY_HTTP_METHOD_LIST => [
                        Configuration::HTTP_METHOD_GET,
                    ],
                ]),
                'url' => 'http://example.com/',
                'expectedLinkState' => new LinkState(
                    LinkState::TYPE_HTTP,
                    999
                ),
            ],
        ];
    }

    /**
     * @throws GuzzleException
     */
    public function testCheckWithReferrer()
    {
        $this->appendHttpFixtures([
            new Response(),
        ]);

        $referrer = 'foo';

        $this->urlHealthChecker->setConfiguration(new Configuration([
            Configuration::CONFIG_KEY_REFERRER => $referrer,
        ]));

        $this->urlHealthChecker->check('http://example.com');

        $this->assertEquals($referrer, $this->httpHistory->getLastRequest()->getHeader('referer')[0]);
    }

    /**
     * @throws GuzzleException
     */
    public function testCheckWithUserAgentSelection()
    {
        $this->appendHttpFixtures([
            new Response(),
        ]);

        $this->urlHealthChecker->setConfiguration(new Configuration([
            Configuration::CONFIG_KEY_USER_AGENTS => [
                'foo',
                'bar',
            ],
        ]));

        $this->urlHealthChecker->check('http://example.com');

        $this->assertEquals('foo', $this->httpHistory->getLastRequest()->getHeader('user-agent')[0]);
    }

    /**
     * @param array $httpFixtures
     */
    private function appendHttpFixtures(array $httpFixtures)
    {
        foreach ($httpFixtures as $httpFixture) {
            $this->mockHandler->append($httpFixture);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function assertPostConditions()
    {
        parent::assertPostConditions();

        $this->assertEquals(0, $this->mockHandler->count());
    }
}
