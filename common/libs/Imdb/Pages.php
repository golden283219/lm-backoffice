<?php

namespace common\libs\Imdb;

use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Handles requesting urls, including the caching layer
 */
class Pages
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    protected $pages = array();
    protected $name;

    /**
     * @param Config $config
     * @param CacheInterface $cache
     * @param LoggerInterface $logger
     */
    public function __construct(Config $config, CacheInterface $cache, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->cache = $cache;
        $this->logger = $logger;
    }

    /**
     * Retrieve the content of the specified $url
     * Caching will be used where possible
     * @param $url
     * @return string
     * @throws Exception\Http
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($url)
    {
        if (!empty($this->pages[$url])) {
            return $this->pages[$url];
        }

        if ($this->pages[$url] = $this->getFromCache($url)) {
            return $this->pages[$url];
        }

        if ($this->pages[$url] = $this->requestPage($url)) {
            $this->saveToCache($url, $this->pages[$url]);
            return $this->pages[$url];
        } else {
            // failed to get page
            return '';
        }
    }

    /**
     * Request the page from IMDb
     * @param $url
     * @return string Page html. Empty string on failure
     * @throws Exception\Http
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function requestPage($url)
    {
        $this->logger->info("[Page] Requesting [$url]");
        $req = $this->buildRequest($url);
        if (!$req->sendRequest()) {
            $this->logger->error("[Page] Failed to connect to server when requesting url [$url]");
            if ($this->config->throwHttpExceptions) {
                throw new Exception\Http("Failed to connect to server when requesting url [$url]");
            } else {
                return '';
            }
        }

        return $req->getResponseBody();
    }

    protected function getFromCache($url)
    {
        return $this->cache->get($this->getCacheKey($url));
    }

    protected function saveToCache($url, $page)
    {
        $this->cache->set($this->getCacheKey($url), $page, $this->config->cache_expire);
    }

    protected function getCacheKey($url)
    {
        $urlParts = parse_url($url);
        $cacheKey = trim($urlParts['path'], '/') . (isset($urlParts['query']) ? '?' . $urlParts['query'] : '');
        return str_replace(array('{', '}', '(', ')', '/', '\\', '@', ':'), '.', $cacheKey);
    }

    protected function buildRequest($url)
    {
        return new RequestGuzzle($url, $this->config);
    }

}
