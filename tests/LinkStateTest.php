<?php

namespace webignition\Tests\UrlHealthChecker;

use webignition\UrlHealthChecker\LinkState;

class LinkStateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $type
     * @param int $state
     */
    public function testCreate($type, $state)
    {
        $linkState = new LinkState($type, $state);

        $this->assertEquals($type, $linkState->getType());
        $this->assertEquals($state, $linkState->getState());
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            'curl 28' => [
                'type' => LinkState::TYPE_CURL,
                'state' => 28,
            ],
            'http 404' => [
                'type' => LinkState::TYPE_HTTP,
                'state' => 404,
            ],
        ];
    }

    /**
     * @dataProvider isErrorDataProvider
     *
     * @param LinkState $linkState
     * @param bool $expectedIsError
     */
    public function testIsError(LinkState $linkState, bool $expectedIsError)
    {
        $this->assertEquals($expectedIsError, $linkState->isError());
    }

    public function isErrorDataProvider(): array
    {
        return [
            'curl state is error' => [
                'linkState' => new LinkState(LinkState::TYPE_CURL, 6),
                'expectedIsError' => true,
            ],
            'http 300 is error' => [
                'linkState' => new LinkState(LinkState::TYPE_HTTP, 300),
                'expectedIsError' => true,
            ],
            'http 400 is error' => [
                'linkState' => new LinkState(LinkState::TYPE_HTTP, 400),
                'expectedIsError' => true,
            ],
            'http 200 is not error' => [
                'linkState' => new LinkState(LinkState::TYPE_HTTP, 200),
                'expectedIsError' => false,
            ],
        ];
    }
}
