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

    public function __construct(string $type, int $state)
    {
        $this->type = $type;
        $this->state = $state;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getState(): int
    {
        return $this->state;
    }
}
