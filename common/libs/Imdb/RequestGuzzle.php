<?php

namespace common\libs\Imdb;

use Codeception\Module\Cli;
use GuzzleHttp\Client;

/**
 * The request class
 * Here we emulate a browser accessing the IMDB site. You don't need to
 * call any of its method directly - they are rather used by the IMDB classes.
 */
class RequestGuzzle
{
    private $client;
    private $lastResponse;
    private $ch;
    private $urltoopen;
    private $page;
    private $requestHeaders = array();
    private $responseHeaders = array();
    private $config;

    /**
     * No need to call this.
     * @param string $url URL to open
     * @param Config $config The Config object to use
     */
    public function __construct($url, Config $config)
    {
        $this->client = new Client();
        $this->config = $config;

        $this->urltoopen = $url;

        $this->addHeaderLine('Referer', 'https://' . $config->imdbsite . '/');

        if ($config->force_agent) {
            $this->addHeaderLine('User-Agent', $config->force_agent);
        } else {
            $this->addHeaderLine('User-Agent', $config->default_agent);
        }
        if ($config->language) {
            $this->addHeaderLine('Accept-Language', $config->language);
        }
        if ($config->ip_address) {
            $this->addHeaderLine('X-Forwarded-For', $config->ip_address);
        }
        // Disable the new site layout
        $this->addHeaderLine('Cookie', 'beta-control=""');
    }

    public function addHeaderLine($name, $value)
    {
        $this->requestHeaders[] = "$name: $value";
    }

    /**
     * Send a request to the movie site
     * @return boolean success
     * @throws Exception\Http
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest()
    {
        $this->responseHeaders = array();

        $options = [];
        $options['headers'] = $this->requestHeaders;

        if ($this->config->use_proxy) {
            $options['proxy'] = 'http://'.$this->config->proxy_host.':'.$this->config->proxy_port;
        }

        $response = $this->client->request('GET', $this->urltoopen, $options);

        $this->responseHeaders = $response->getHeaders();

        $this->page = $response->getBody();

        if ($this->page !== false) {
            return true;
        }

        return false;
    }

    /**
     * Get the Response body
     * @return string page
     */
    public function getResponseBody()
    {
        return $this->page;
    }

    /**
     * Set the URL we need to parse
     * @param string $url
     */
    public function setURL($url)
    {
        $this->urltoopen = $url;
    }

    /**
     * Get a header value from the response
     * @param string $header header field name
     * @return string header value
     */
    public function getResponseHeader($header)
    {
        if (!empty($this->responseHeaders[$header])) {
            return $this->responseHeaders[$header];
        }

        return null;
    }

    /**
     * HTTP status code of the last response
     * @return int|null null if last request failed
     */
    public function getStatus()
    {
        if (empty($this->lastResponse)) {
            return null;
        }

        return $this->lastResponse->getStatusCode();
    }

    public function getLastResponseHeaders()
    {
        return $this->responseHeaders;
    }
}
