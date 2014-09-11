<?php

namespace webignition\Tests\UrlHealthChecker;

use Guzzle\Http\Client as HttpClient;
use Guzzle\Plugin\History\HistoryPlugin;

use Guzzle\Plugin\Cookie\CookiePlugin;
use Guzzle\Plugin\Cookie\CookieJar\ArrayCookieJar;
use Guzzle\Plugin\Cookie\Cookie;

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


//            $cookieJar = new ArrayCookieJar();
//
//            foreach ($this->getCookies() as $cookieData) {
//                $cookieJar->add(new Cookie($cookieData));
//            }
//
//            $cookiePlugin = new CookiePlugin($cookieJar);
//
//            $this->httpClient->addSubscriber($cookiePlugin);
        }

        return $this->httpClient;
    }
}