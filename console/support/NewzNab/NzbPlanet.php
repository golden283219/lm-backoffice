<?php

namespace console\support\NewzNab;

use GuzzleHttp\Client;

class NzbPlanet
{

    protected $client;
    protected $api_url;

    public function __construct($key)
    {
        $this->client = new Client();
        $this->api_url = strtr('https://api.nzbplanet.net/api?t=tvsearch&apikey={@api_key}&o=json&tvmazeid={@tvmazeid}', [
            '{@api_key}' => $key
        ]);
    }

    public function query_term($term)
    {
        $response = false;

        $is_locked = true;
        $try_counter = 0;

        $url = $this->prepare_url($this->api_url, $term);

        while ($is_locked === true && $try_counter < 3) {
            try {
                $guzzleResponse = $this->client->get($url);
                echo 'Sending GET request: ' . $url . PHP_EOL;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $resp = $e->getResponse();
                \Yii::error($resp->getBody(), 'NzbPlanet--Error');
                echo $resp->getBody() . PHP_EOL;
                sleep(3600);
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                $resp = $e->getResponse();
                \Yii::error($resp->getBody(), 'NzbPlanet--Error');
                echo $resp->getBody() . PHP_EOL;
                sleep(3600);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                $resp = $e->getResponse();
                \Yii::error($resp->getBody(), 'NzbPlanet--Error');
                echo $resp->getBody() . PHP_EOL;
                sleep(3600);
            } catch (\Exception $e) {
                \Yii::error($e->getMessage(), 'NzbPlanet--Error');
                echo $e->getMessage() . PHP_EOL;
            }

            $try_counter++;
            if (isset($guzzleResponse) && $guzzleResponse->getStatusCode() === 200) {
                $is_locked = false;
                $data = $guzzleResponse->getBody();

                $resp = $this->parse_response(\GuzzleHttp\json_decode($data));
                if (isset($resp) && count($resp) > 0) {
                    $response = true;
                }
            } else {
                sleep(15);
            }
        }

        return $response;

    }

    public function parse_response($parsed)
    {

        $response = [];

        if (!isset($parsed->channel->item)) {
            return $response;
        }

        if (is_array($parsed->channel->item)) {
            foreach ($parsed->channel->item as $item) {
                $response[] = [
                    'title' => $item->title,
                    'link' => $item->link,
                    'guid' => (function ($guid) {
                        $re = '/.+\/(.+)/m';
                        preg_match_all($re, $guid, $matches, PREG_SET_ORDER, 0);
                        return $matches['0']['1'];
                    })($item->guid)
                ];
            }
        } else {
            $response[] = [
                'title' => $parsed->channel->item->title,
                'link' => $parsed->channel->item->link,
                'guid' => (function ($guid) {
                    $re = '/.+\/(.+)/m';
                    preg_match_all($re, $guid, $matches, PREG_SET_ORDER, 0);
                    return $matches['0']['1'];
                })($parsed->channel->item->guid)
            ];
        }
        return $response;
    }

    private function prepare_url ($url, $tvmaze_id)
    {
        return strtr($url, [
            '{@tvmazeid}' => $tvmaze_id,
        ]);
    }
}
