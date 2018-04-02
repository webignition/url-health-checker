<?php

namespace webignition\Tests\UrlHealthChecker;

use webignition\UrlHealthChecker\LinkState;

class LinkStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider testCreateDataProvider
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
    public function testCreateDataProvider()
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
}
