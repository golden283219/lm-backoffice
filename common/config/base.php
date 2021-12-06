<?php

$config = [
    'name' => 'lookmovie backoffice',
    'vendorPath' => __DIR__ . '/../../vendor',
    'timeZone' => 'Europe/Istanbul',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'sourceLanguage' => 'en-US',
    'language' => 'en-US',
    'bootstrap' => [
        'log',
        'queue',
        'metadataDownloadQueue',
        'downloadsListUpdateQueue',
        'castMetadataDownloadQueue',
        'putIoMaintenance',
        'downloadQueueTasks',
        'emailSendQueue'
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@node_modules' => '@base/node_modules'
    ],
    'components' => [
        'redis' => [
            'class' => \yii\redis\Connection::class,
            'hostname' => env('REDIS_HOST'),
            'port'     => env('REDIS_PORT'),
            'database' => 0,
            'retries' => 1,
        ],
        'authManager' => [
            'class' => yii\rbac\DbManager::class,
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}'
        ],

        // Metadata Download Queue
        'metadataDownloadQueue' => [
            'class' => \common\components\queue\RabbitMQHandler::class,
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASS', 'guest'),
            'queueName' => 'metadata_download_queue',
            'exchangeName' => 'metadata_download_queue',
            'as log' => \yii\queue\LogBehavior::class,
            'attempts' => 15,
            'ttr' => 1020,
            'commandClass' => '\console\commands\MetadataDownloadQueueCommand'
        ],

        // we have separate queue to download
        // cast metadata, because we want to do
        // ir for entire site
        'castMetadataDownloadQueue' => [
            'class' => \common\components\queue\RabbitMQHandler::class,
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASS', 'guest'),
            'queueName' => 'cast_metadata_download_queue',
            'exchangeName' => 'cast_metadata_download_queue',
            'as log' => \yii\queue\LogBehavior::class,
            'attempts' => 15,
            'ttr' => 1020,
            'commandClass' => '\console\commands\CastMetadataDownloadQueueCommand'
        ],

        /**
         * This queue performs all operations related
         * to movies and shows download queue
         */
        'downloadsListUpdateQueue' => [
            'class' => \common\components\queue\RabbitMQHandler::class,
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASS', 'guest'),
            'queueName' => 'downloads_list_update_queue',
            'exchangeName' => 'downloads_list_update_queue',
            'as log' => \yii\queue\LogBehavior::class,
            'attempts' => 15,
            'ttr' => 1020,
            'commandClass' => \console\commands\DownloadQueueMaintenanceCommands::class
        ],

        /**
         * Email Send Queue
         */
        'emailSendQueue' => [
            'class' => \common\components\queue\RabbitMQHandler::class,
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASS', 'guest'),
            'queueName' => 'email_send_queue',
            'exchangeName' => 'email_send_queue',
            'as log' => \yii\queue\LogBehavior::class,
            'attempts' => 15,
            'ttr' => 1020
        ],

        /**
         * Process Download Queue background tasks
         */
        'downloadQueueTasks' => [
            'class' => \common\components\queue\RabbitMQHandler::class,
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASS', 'guest'),
            'queueName' => 'download_queue_tasks',
            'exchangeName' => 'download_queue_tasks',
            'as log' => \yii\queue\LogBehavior::class,
            'attempts' => 15,
            'ttr' => 1020
        ],

        // PutIO Maintenance Queue
        'putIoMaintenance' => [
            'class' => \common\components\queue\RabbitMQHandler::class,
            'host' => env('RABBITMQ_HOST', 'localhost'),
            'port' => env('RABBITMQ_PORT', 5672),
            'user' => env('RABBITMQ_USER', 'guest'),
            'password' => env('RABBITMQ_PASS', 'guest'),
            'queueName' => 'put_io_maintenance',
            'exchangeName' => 'put_io_maintenance',
            'as log' => \yii\queue\LogBehavior::class,
            'attempts' => 15,
            'ttr' => 1020,
            'commandClass' => \console\commands\PutIoMaintenanceCommands::class
        ],

        'cache' => [
            'class' => yii\caching\FileCache::class,
            'cachePath' => '@common/runtime/cache'
        ],

        'formatter' => [
            'class' => yii\i18n\Formatter::class,
            'defaultTimeZone' => 'Europe/Istanbul',
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp-relay.sendinblue.com',
                'username' => 'jsonbrawn@protonmail.ch',
                'password' => 'j5XBRcSVbsmT0tKO',
                'port' => '587'
            ],
        ],

        'db' => require __DIR__ . '/db/db.php',

        'db_imdb' => require __DIR__ . '/db/db_imdb.php',

        'db_queue' => require __DIR__ . '/db/db_queue.php',

        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error'],
                    'except' => ['yii\web\HttpException:*', 'yii\i18n\I18N\*'],
                    'prefix' => function () {
                        $url = !Yii::$app->request->isConsoleRequest && Yii::$app->request->getUrl() !== '/system/write-log' ? Yii::$app->request->getUrl() : null;
                        return sprintf('%s %s', Yii::$app->id, $url);
                    },
                    'logVars' => [],
                    'logTable' => '{{%system_log}}'
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => ['yii\web\HttpException:*', 'yii\i18n\I18N\*', 'yii\db\*', 'yii\web\*', 'yii\filters\*'],
                    'prefix' => function () {
                        $url = !Yii::$app->request->isConsoleRequest && Yii::$app->request->getUrl() !== '/system/write-log' ? Yii::$app->request->getUrl() : null;
                        return sprintf('%s %s', Yii::$app->id, $url);
                    },
                    'logVars' => [],
                    'logTable' => '{{%system_log}}'
                ]
            ],
        ],

        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
                '*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'common' => 'common.php',
                        'backend' => 'backend.php'
                    ],
                    'on missingTranslation' => [backend\modules\translation\Module::class, 'missingTranslation']
                ],
            ],
        ],

        'keyStorage' => [
            'class' => common\components\keyStorage\KeyStorage::class
        ],

        'imageStorage' => [
            'class' => common\components\imageStorage\ImageStorage::class
        ],

        'urlManagerBackend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => env('BACKEND_HOST_INFO'),
                'baseUrl' => env('BACKEND_BASE_URL'),
            ],
            require(Yii::getAlias('@backend/config/_urlManager.php'))
        ),
        'urlManagerStorage' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => env('STORAGE_HOST_INFO'),
                'baseUrl' => env('STORAGE_BASE_URL'),
            ],
            require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),

        'urlManagerFrontend' => \yii\helpers\ArrayHelper::merge(
            [
                'hostInfo' => env('STORAGE_HOST_INFO'),
                'baseUrl' => env('STORAGE_BASE_URL'),
            ],
            require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),

        'queue' => [
            'class' => \yii\queue\file\Queue::class,
            'path' => '@common/runtime/queue',
        ],
    ],

    'params' => [
        'adminEmail' => env('ADMIN_EMAIL'),
        'availableLocales' => [
            'en-US' => 'English (US)'
        ],
        'titleService' => [
            'movies' => env('MOVIES_TITLE_API_URL'),
            'shows' => env('SHOWS_TITLE_API_URL'),
        ]
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class
    ];

    $config['components']['cache'] = [
        'class' => yii\caching\DummyCache::class
    ];
}

return $config;
