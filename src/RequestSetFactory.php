<?php

namespace webignition\UrlHealthChecker;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Url as GuzzleUrl;
use GuzzleHttp\Query as GuzzleQuery;

class RequestSetFactory
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string $url
     *
     * @return RequestInterface[]
     */
    public function create($url)
    {
        $useEncodingOptions = ($this->configuration->getToggleUrlEncoding())
            ? array(true, false)
            : array(true);

        $requests = array();

        $userAgentSelection = $this->configuration->getUserAgentSelectionForRequest();
        $httpClient = $this->configuration->getHttpClient();
        $referrer = $this->configuration->getReferrer();

        foreach ($userAgentSelection as $userAgent) {
            foreach ($this->configuration->getHttpMethodList() as $methodIndex => $method) {
                foreach ($useEncodingOptions as $useEncoding) {
                    $requests[] = $this->createRequest(
                        $httpClient,
                        $method,
                        $url,
                        $useEncoding,
                        $userAgent,
                        $referrer
                    );
                }
            }
        }

        return $requests;
    }

    /**
     * @param HttpClient $httpClient
     * @param string $method
     * @param string $url
     * @param bool $encodeQuery
     * @param string $userAgent
     * @param string $referrer
     *
     * @return RequestInterface
     */
    private function createRequest(HttpClient $httpClient, $method, $url, $encodeQuery, $userAgent, $referrer)
    {
        $requestUrl = GuzzleUrl::fromString($url);
        $encodingType = $encodeQuery ? GuzzleQuery::RFC3986 : false;

        $request = $httpClient->createRequest(
            $method,
            $requestUrl
        );

        $request->getQuery()->setEncodingType($encodingType);

        $request->setHeader('user-agent', $userAgent);

        if (!empty($referrer)) {
            $request->setHeader('Referer', $referrer);
        }

        return $request;
    }
}
