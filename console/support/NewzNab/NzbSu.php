<?php

namespace console\support\NewzNab;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class NzbSu
{

    protected $client;
    protected $api_url;

    public function __construct($key)
    {
        $this->client = new Client([
            'cookies' => true
        ]);

        $this->api_url = strtr('https://api.nzb.su/api?t=tvsearch&apikey={@api_key}&o=json&tvmazeid={@tvmazeid}', [
            '{@api_key}' => $key
        ]);
    }

    public function query_term($tvmaze_id)
    {
        $response = false;

        $is_locked = true;
        $try_counter = 0;

        $url = $this->prepare_url($this->api_url, $tvmaze_id);

        while ($is_locked === true && $try_counter < 3) {
            try {
                $headers = [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36 OPR/62.0.3331.116'
                ];
                $request = new Request('GET', $url, $headers);
                $guzzleResponse = $this->client->send($request);
                echo 'Sending GET request: ' . $url . PHP_EOL;
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $resp = $e->getResponse();
                \Yii::error($resp->getBody(), 'NzbSu--Error');
                echo $resp->getBody() . PHP_EOL;
                sleep(3600);
            } catch (\GuzzleHttp\Exception\ServerException $e) {
                $resp = $e->getResponse();
                \Yii::error($resp->getBody(), 'NzbSu--Error');
                echo $resp->getBody() . PHP_EOL;
                sleep(3600);
            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                $resp = $e->getResponse();
                \Yii::error($resp->getBody(), 'NzbSu--Error');
                echo $resp->getBody() . PHP_EOL;
                sleep(3600);
            } catch (\Exception $e) {
                \Yii::error($e->getMessage(), 'NzbSu--Error');
            }

            $try_counter++;
            if (isset($guzzleResponse) && $guzzleResponse->getStatusCode() === 200) {
                $is_locked = false;
                $data = $guzzleResponse->getBody();

                $resp = $this->parse_response(\GuzzleHttp\json_decode($data));
                if (isset($resp) && count($resp)) {
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
            '{@tvmazeid}' => $tvmaze_id
        ]);
    }
}
