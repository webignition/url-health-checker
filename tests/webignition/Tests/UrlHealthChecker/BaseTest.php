<?php

namespace webignition\Tests\UrlHealthChecker;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Subscriber\History as HttpHistorySubscriber;

abstract class BaseTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     * @var HttpClient
     */
    private $httpClient;


    /**
     * @param array $options
     * @return HttpClient
     */
    protected function getHttpClient($options = []) {
        if (is_null($this->httpClient)) {
            $this->httpClient = new HttpClient($options);
            $this->httpClient->getEmitter()->attach(new HttpHistorySubscriber());
        }

        return $this->httpClient;
    }
}