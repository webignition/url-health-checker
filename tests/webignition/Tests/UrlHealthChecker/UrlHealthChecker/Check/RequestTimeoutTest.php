<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

use webignition\WebResource\WebPage\WebPage;
use webignition\HtmlDocument\LinkChecker\LinkResult;
use webignition\HtmlDocument\LinkChecker\LinkState;

class RequestTimeoutTest extends CheckTest {

    protected function preCall() {
        $baseRequest = $this->getHttpClient()->createRequest('GET', $this->getRequestUrl(), array(), null, array(
            'timeout'         => 0.001,
            'connect_timeout' => 0.001
        ));

        $this->getUrlHealthChecker()->getConfiguration()->setBaseRequest($baseRequest);
    }

    protected function getRequestUrl() {
        return 'http://example.com/';
    }

    protected function getHttpFixtures() {
        return [];
    }

    protected function getExpectedLinkStateType() {
        return 'curl';
    }

    protected function getExpectedResponseCode() {
        return 28;
    }
    
}