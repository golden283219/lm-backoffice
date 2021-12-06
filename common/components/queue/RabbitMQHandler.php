<?php

namespace common\components\queue;

use console\models\MetadataScraperProxyServers;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQHandler extends \yii\queue\amqp\Queue
{
    /**
     * Listens amqp-queue and runs new jobs.
     */
    public function listen()
    {
        $proxy_id = MetadataScraperProxyServers::queryMostAvailable();
        $proxy_server = MetadataScraperProxyServers::find()->where(['id' => $proxy_id])->one();
        $this->open();

        $callback = function (AMQPMessage $payload) use ($proxy_server) {
            $id = $payload->get('message_id');
            list($ttr, $message) = explode(';', $payload->body, 2);
            $messageObj = unserialize($message);
            if (!empty($proxy_server) && property_exists($messageObj, 'injectedProxy')) {
                $messageObj->injectedProxy = $proxy_server->proxy_url;
                $message = serialize($messageObj);
            }

            if ($this->handleMessage($id, $message, $ttr, 1)) {
                $payload->delivery_info['channel']->basic_ack($payload->delivery_info['delivery_tag']);
            }
        };
        $this->channel->basic_qos(null, 1, null);
        $this->channel->basic_consume($this->queueName, '', false, false, false, false, $callback);
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }
}
