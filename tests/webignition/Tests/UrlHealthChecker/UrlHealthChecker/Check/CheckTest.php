<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

use webignition\Tests\UrlHealthChecker\BaseTest;
use GuzzleHttp\Message\Response as HttpResponse;
use GuzzleHttp\Subscriber\History as HttpHistorySubscriber;
use GuzzleHttp\Subscriber\Mock as HttpMockSubscriber;
use webignition\UrlHealthChecker\UrlHealthChecker;
use webignition\UrlHealthChecker\LinkState;

abstract class CheckTest extends BaseTest {


    abstract protected function getHttpFixtures();
    abstract protected function getRequestUrl();
    abstract protected function getExpectedLinkStateType();
    abstract protected function getExpectedResponseCode();

    /**
     * @var UrlHealthChecker
     */
    private $urlHealthChecker;


    /**
     * @var LinkState
     */
    private $linkState;


    public function setUp() {
        if (count($this->getHttpFixtures())) {
            $this->getHttpClient()->getEmitter()->attach(new HttpMockSubscriber($this->getHttpFixtures()));
        }

        $this->preConstructHealthChecker();

        $this->urlHealthChecker = new UrlHealthChecker();
        $this->urlHealthChecker->getConfiguration()->setHttpClient($this->getHttpClient());
        $this->urlHealthChecker->getConfiguration()->setBaseRequest($this->getHttpClient()->createRequest('GET'));
        $this->urlHealthChecker->getConfiguration()->disableRetryOnBadResponse();
        $this->urlHealthChecker->getConfiguration()->setHttpMethodList(array('GET'));

        $this->preCall();

        $this->linkState = $this->urlHealthChecker->check($this->getRequestUrl());
    }

    public function testLinkStateTypeMatchesExpectedType() {
        $this->assertEquals($this->getLinkState()->getType(), $this->getExpectedLinkStateType());
    }


    public function testLinkStateMatchesResponseCode() {
        $this->assertEquals($this->getLinkState()->getState(), $this->getExpectedResponseCode());
    }


    /**
     * @return LinkState
     */
    protected function getLinkState() {
        return $this->linkState;
    }


    protected function preConstructHealthChecker() {
    }


    protected function preCall() {
    }


    /**
     * @return UrlHealthChecker
     */
    protected function getUrlHealthChecker() {
        return $this->urlHealthChecker;
    }


    /**
     *
     * @return HttpHistorySubscriber|null
     */
    protected function getHttpHistory() {
        $listenerCollections = $this->getHttpClient()->getEmitter()->listeners('complete');

        foreach ($listenerCollections as $listener) {
            if ($listener[0] instanceof HttpHistorySubscriber) {
                return $listener[0];
            }
        }

        return null;
    }


}