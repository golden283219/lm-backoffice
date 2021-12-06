<?php

$config = [
    'homeUrl' => Yii::getAlias('@apiUrl'),
    'controllerNamespace' => 'api\controllers',
    'defaultRoute' => 'site/index',
    'bootstrap' => [
        'maintenance',
    ],
    'modules' => [
        'v1' => \api\modules\v1\Module::class,
    ],
    'components' => [
        'redisNotificationsQueue' => [
            'class' => \yii\queue\redis\Queue::class,
            'as log' => \yii\queue\LogBehavior::class, //The default error log is console/runtime/logs/app.log
            'attempts' => 5, // Max number of attempts
            'ttr' => 1020,  // max TTL of process - since API call can take upto 15 minutes extra 2 minutes for the rest of processes
            'channel' => 'notifications', // Queue channel key,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error'
        ],
        'maintenance' => [
            'class' => common\components\maintenance\Maintenance::class,
            'enabled' => function ($app) {
                if (env('APP_MAINTENANCE') === '1') {
                    return true;
                }
                return $app->keyStorage->get('frontend.maintenance') === 'enabled';
            }
        ],
        'request' => [
            'enableCookieValidation' => false,
        ],
        'user' => [
            'class' => yii\web\User::class,
            'identityClass' => common\models\User::class,
            'loginUrl' => ['/user/sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => common\behaviors\LoginTimestampBehavior::class
        ],

    ]
];

return $config;
