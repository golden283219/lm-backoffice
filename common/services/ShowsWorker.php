<?php


namespace common\services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class ShowsWorker
{

    /**
     * @var $base_uri string remote server ip
     */
    private $base_uri;

    /**
     * @var $client string GuzzleHttp Client
     */
    private $client;

    public function __construct($base_uri)
    {
        $this->base_uri = $base_uri;

        $this->client = new Client([
            'base_uri' => $this->base_uri,
            'timeout'  => 5.0
        ]);
    }

    /**
     * @return mixed
     */
    public function get_health()
    {
        $path = '/system/health';

        return json_decode($this->client->get($path)->getBody());
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function get_process_list($page = 1, $per_page = 10)
    {
        $path = "/process/list?page=$page&per-page=$per_page";

        return json_decode($this->client->get($path)->getBody());
    }

    /**
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function async_get_health()
    {
        $path = '/system/health';

        return $this->client->getAsync($path);
    }

    /**
     * @param int $page
     * @param int $per_page
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function async_get_process_list($page = 1, $per_page = 10)
    {
        $path = "/process/list?page=$page&per-page=$per_page";

        return $this->client->getAsync($path);
    }

    /**
     * @param array $promises
     * @return array
     * @throws \Throwable
     */
    public static function wrap_promises(array $promises)
    {
        $response_array = [];

        $responses = Promise\unwrap($promises);

        foreach ($responses as $key => $response) {
            $response_array[$key] = json_decode($response->getBody());
        }

        return $response_array;
    }
}