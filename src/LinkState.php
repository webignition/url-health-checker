<?php

namespace webignition\UrlHealthChecker;

class LinkState
{
    const TYPE_HTTP = 'http';
    const TYPE_CURL = 'curl';

    /**
     * @var string
     */
    private $type = null;

    /**
     * @var int
     */
    private $state = null;

    /**
     * @param string $type
     * @param int $state
     */
    public function __construct($type, $state)
    {
        $this->type = $type;
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }
}
