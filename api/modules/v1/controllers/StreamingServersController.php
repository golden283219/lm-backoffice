<?php

namespace api\modules\v1\controllers;

use common\models\FeServers;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\Serializer;
use yii\web\HttpException;

class StreamingServersController extends ActiveController
{
    /**
     * @var string
     */
    public $modelClass = 'api\modules\v1\resources\Movies';
    /**
     * @var array
     */
    public $serializer = [
        'class' => Serializer::class,
        'collectionEnvelope' => 'items'
    ];

    public function actionNotify()
    {
        $response = [
            'success' => false
        ];

        $post = json_decode(file_get_contents('php://input'));
        $post = is_array($post) && count($post) > 0 ? $post['0'] : $post;

        $url  = ArrayHelper::getValue($post, 'check.url');
        $domain = extract_domain_from_url($url);

        if (empty($post) || empty($domain)) {
            throw new HttpException(400, 'Missing Required Parameters. ' . json_encode($post, JSON_PRETTY_PRINT));
        }

        switch ($post->event) {
            case 'check.up':
                $response['success'] = true;
                $response['message'] = 'CDN is up. Switched to CDN.';
                $this->cdnUP($domain);
                break;
            case 'check.down':
                $response['success'] = true;
                $response['message'] = 'CDN is down. Switched To Regular Servers';
                $this->cdnDOWN($domain);
                break;
        }

        return $response;
    }

    // Execute command in case cdn is UP again
    private function cdnUP($domain = '')
    {
        $ips = ['45.134.21.20', '45.134.21.21', '45.134.21.162'];

        $domains = [
            'jourchyard.xyz' => [
                'ip'               => '45.134.21.20',
                'server_name'      => 'cache 1 (cnd1)',
                'max_bw'           => 10000,
                'is_enabled'       => 1,
                'status_check_url' => 'http://45.134.21.20:8322/',
                'is_hidden'        => 1,
                'domain_mapped'    => ["lookmovie.ag" => "https://aqa.peekented.cyou", "lookmovie.io" => "https://aqa.peekented.cyou"],
            ],
            'ultatic.xyz' => [
                'ip'               => '45.134.21.21',
                'server_name'      => 'cache2 (cdn2)',
                'max_bw'           => 10000,
                'is_enabled'       => 1,
                'status_check_url' => 'http://45.134.21.21:8322/',
                'is_hidden'        => 1,
                'domain_mapped'    => ["lookmovie.ag" => "https://aqb.peekented.cyou", "lookmovie.io" => "https://aqb.peekented.cyou"],
            ],
            'restumment.cyou' => [
                'ip'               => '45.134.21.162',
                'server_name'      => 'cache 3 (cdn3)',
                'max_bw'           => 10000,
                'is_enabled'       => 1,
                'status_check_url' => 'http://45.134.21.162:8322/',
                'is_hidden'        => 1,
                'domain_mapped'    => ["lookmovie.ag" => "https://aqc.peekented.cyou", "lookmovie.io" => "https://aqc.peekented.cyou"],
            ],
        ];

        $this->updateDomains($domains, $ips);
    }

    // Execute command in case cdn is down
    private function cdnDOWN($domain = '')
    {
        $ips = ['45.134.21.20', '45.134.21.21', '45.134.21.162'];

        $domains = [
            'jourchyard.xyz' => [
                'ip'               => '45.134.21.20',
                'server_name'      => 'cache 1 (cnd1)',
                'max_bw'           => 10000,
                'is_enabled'       => 1,
                'status_check_url' => 'http://45.134.21.20:8322/',
                'is_hidden'        => 1,
                'domain_mapped'    => ["lookmovie.ag" => "https://vdc1.cedomy.xyz", "lookmovie.io" => "https://vdc1.cedomy.xyz"],
            ],
            'ultatic.xyz' => [
                'ip'               => '45.134.21.21',
                'server_name'      => 'cache2 (cdn2)',
                'max_bw'           => 10000,
                'is_enabled'       => 1,
                'status_check_url' => 'http://45.134.21.21:8322/',
                'is_hidden'        => 1,
                'domain_mapped'    => ["lookmovie.ag" => "https://vdc2.cedomy.xyz", "lookmovie.io" => "https://vdc2.cedomy.xyz"],
            ],
            'restumment.cyou' => [
                'ip'               => '45.134.21.162',
                'server_name'      => 'cache 3 (cdn3)',
                'max_bw'           => 10000,
                'is_enabled'       => 1,
                'status_check_url' => 'http://45.134.21.162:8322/',
                'is_hidden'        => 1,
                'domain_mapped'    => ["lookmovie.ag" => "https://vdc3.cedomy.xyz", "lookmovie.io" => "https://vdc3.cedomy.xyz"],
            ],
        ];

        $this->updateDomains($domains, $ips);
    }

    private function updateDomain($domain)
    {
        $fe_servers = FeServers::find()->all();

        foreach ($fe_servers as $fe_server) {
            if (trim($fe_server->ip) == trim($domain['ip'])) {
                $fe_server->delete();
            }
        }

        $fe_server = new FeServers;
        $fe_server->ip = $domain['ip'];
        $fe_server->server_name = $domain['server_name'];
        $fe_server->is_enabled = $domain['is_enabled'];
        $fe_server->max_bw = $domain['max_bw'];
        $fe_server->status_check_url = $domain['status_check_url'];
        $fe_server->is_hidden = $domain['is_hidden'];
        $fe_server->domain_mapped = $domain['domain_mapped'];

        if (!$fe_server->validate() || !$fe_server->save()) {
            throw new Exception('Unable To save streaming server', $fe_server->errors);
        }

        $servers = \backend\modules\system\models\FeServers::find()->where(['is_enabled' => '1'])->asArray()->all();

        $servers_formatted = array_map(function ($item) {
            return [
                'ip' => $item['ip'],
                'server_name' => $item['server_name'],
                'status' => $item['status_check_url'],
                'domain_mapped' => isset($item['domain_mapped']) ? $item['domain_mapped'] : [],
                'maxBW' => $item['max_bw']
            ];
        }, $servers);

        backup_config();

        if (file_put_contents(env('LB_CONFIG_PATH'), \GuzzleHttp\json_encode($servers_formatted)) && lb_api_do_config_update()) {
            \Yii::$app->getSession()->setFlash('success', "All Servers Have Been Dumped");
        } else {
            restore_config_backup();
            \Yii::$app->getSession()->setFlash('error', 'Something Went Wrong while Dumping. Backup config have been restored');
        }
    }

    private function updateDomains($domains, $ips)
    {
        $fe_servers = FeServers::find()->all();

        foreach ($fe_servers as $fe_server) {
            if (in_array(trim($fe_server->ip), $ips)) {
                $fe_server->delete();
            }
        }

        foreach ($domains as $domain) {
            $fe_server = new FeServers;
            $fe_server->ip = $domain['ip'];
            $fe_server->server_name = $domain['server_name'];
            $fe_server->is_enabled = $domain['is_enabled'];
            $fe_server->max_bw = $domain['max_bw'];
            $fe_server->status_check_url = $domain['status_check_url'];
            $fe_server->is_hidden = $domain['is_hidden'];
            $fe_server->domain_mapped = $domain['domain_mapped'];

            if (!$fe_server->validate() || !$fe_server->save()) {
                throw new Exception('Unable To save streaming server', $fe_server->errors);
            }
        }


        $servers = \backend\modules\system\models\FeServers::find()->where(['is_enabled' => '1'])->asArray()->all();

        $servers_formatted = array_map(function ($item) {
            return [
                'ip' => $item['ip'],
                'server_name' => $item['server_name'],
                'status' => $item['status_check_url'],
                'domain_mapped' => isset($item['domain_mapped']) ? $item['domain_mapped'] : [],
                'maxBW' => $item['max_bw']
            ];
        }, $servers);

        backup_config();

        if (file_put_contents(env('LB_CONFIG_PATH'), \GuzzleHttp\json_encode($servers_formatted)) && lb_api_do_config_update()) {
            \Yii::$app->getSession()->setFlash('success', "All Servers Have Been Dumped");
        } else {
            restore_config_backup();
            \Yii::$app->getSession()->setFlash('error', 'Something Went Wrong while Dumping. Backup config have been restored');
        }

    }

}
