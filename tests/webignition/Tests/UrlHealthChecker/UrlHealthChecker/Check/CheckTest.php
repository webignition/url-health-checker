<?php

namespace webignition\Tests\UrlHealthChecker\UrlHealthChecker\Check;

use webignition\Tests\UrlHealthChecker\BaseTest;
use Guzzle\Http\Message\Response as HttpResponse;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;
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
            $plugin = new MockPlugin();

            foreach ($this->getHttpFixtures() as $item) {
                if ($item instanceof \Exception) {
                    $plugin->addException($item);
                } elseif (is_string($item)) {
                    $plugin->addResponse(HttpResponse::fromMessage($item));
                }
            }

            $this->getHttpClient()->addSubscriber($plugin);
        }

        $this->urlHealthChecker = new UrlHealthChecker();
        $this->urlHealthChecker->getConfiguration()->setBaseRequest($this->getHttpClient()->get());
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
     * @return \Guzzle\Plugin\History\HistoryPlugin|null
     */
    protected function getHttpHistory() {
        $listenerCollections = $this->getHttpClient()->getEventDispatcher()->getListeners('request.sent');

        foreach ($listenerCollections as $listener) {
            if ($listener[0] instanceof HistoryPlugin) {
                return $listener[0];
            }
        }

        return null;
    }


}