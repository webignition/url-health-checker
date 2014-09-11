<?php

namespace webignition\Tests\UrlHealthChecker;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Plugin\History\HistoryPlugin;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var \Guzzle\Http\Client
     */
    private $httpClient;


    /**
     *
     * @return \Guzzle\Http\Client
     */
    protected function getHttpClient() {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient();
            $this->httpClient->addSubscriber(new HistoryPlugin());
        }

        return $this->httpClient;
    }
}