<?php

namespace console\controllers;

use common\models\FeServers;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;

class StreamingMaintenanceController extends Controller
{
    /**
     * @var GuzzleHttp
     */
    private $client;

    /**
     * Calculates and saves to redis avg. BW utilization
     */
    public function actionStoreAvgServersLoad()
    {
        $_fe_servers = FeServers::find()
            ->where(['is_enabled' => 1])
            ->asArray()
            ->all();

        $fe_servers = [];
        foreach ($_fe_servers as $_fe_server) {
            $fe_servers[$_fe_server['id']] = $_fe_server;
        }

        $avg_load = $this->getAvgServersLoad($fe_servers);

        Yii::$app->redis->set('fe.avg_bw_utilization', $avg_load);

        $this->stdout("OK\n");
    }

    /**
     * @param $servers array
     *
     * @return int
     */
    private function getAvgServersLoad($servers)
    {
        $avg_load = 0;

        $this->initGuzzleHttp();

        foreach ($servers as $fe_server_key => $server) {
            $promises[$fe_server_key] = $this->client->getAsync($server['status_check_url']);
        }

        // Wait for the requests to complete, even if some of them fail
        $responses = Promise\Utils::settle($promises)->wait();

        $load = [];
        foreach ($responses as $response_key => $response) {
            if (!empty($response['value']) && $response['value']->getStatusCode()) {
                try {
                    $s_load_response = Json::decode(
                        $response['value']->getBody()
                    );

                    $max_load = intval($servers[$response_key]['max_bw'], 10) * 1000 * 1000 / 8;
                    $current_load = intval($s_load_response['tx']['moment'], 10);

                    $load[] = intval($current_load / $max_load * 100);
                } catch (\Exception $e) {
                    $this->stdout($e->getMessage());
                }
            }
        }

        foreach ($load as $load_value) {
            $avg_load += $load_value;
        }

        return $avg_load / count($load);
    }

    private function initGuzzleHttp()
    {
        if (empty($this->client)) {
            $this->client = new Client();
        }
    }
}
