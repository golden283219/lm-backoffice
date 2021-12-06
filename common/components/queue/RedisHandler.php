<?php

namespace common\components\queue;

use console\models\MetadataScraperProxyServers;

class RedisHandler extends \yii\queue\redis\Queue
{
    public function run($repeat, $timeout = 0)
    {
        $proxy_id = MetadataScraperProxyServers::queryMostAvailable();
        $proxy_server = MetadataScraperProxyServers::find()->where(['id' => $proxy_id])->one();
        return $this->runWorker(function (callable $canContinue) use ($repeat, $timeout, $proxy_server) {
            while ($canContinue()) {
                if (($payload = $this->reserve($timeout)) !== null) {
                    list($id, $message, $ttr, $attempt) = $payload;

                    // query proxy will be used in next scraping process
                    if (!empty($proxy_server)) {
                        $messageObj = unserialize($message);
                        $messageObj->injectedProxy = $proxy_server->proxy_url;
                        $message = serialize($messageObj);
                    }

                    if ($this->handleMessage($id, $message, $ttr, $attempt)) {
                        $this->delete($id);
                    }
                } elseif (!$repeat) {
                    break;
                }
            }
        });
    }
}
