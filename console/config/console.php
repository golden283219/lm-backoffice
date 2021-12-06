<?php
return [
    'id' => 'console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'redisMoviesQueue',
        'redisShowsQueue',
        'redisNotificationsQueue'
    ],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'message' => [
            'class' => console\controllers\ExtendedMessageController::class
        ],
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            'migrationPath' => '@common/migrations/db',
            'migrationTable' => '{{%system_db_migration}}',
        ],
        'rbac-migrate' => [
            'class' => console\controllers\RbacMigrateController::class,
            'migrationPath' => '@common/migrations/rbac/',
            'migrationTable' => '{{%system_rbac_migration}}',
            'templateFile' => '@common/rbac/views/migration.php'
        ],
    ],
    'components' => [
        'redisNotificationsQueue' => [
            'class' => \yii\queue\redis\Queue::class,
            'attempts' => 5, // Max number of attempts
            'ttr' => 1020,  // max TTL of process - since API call can take upto 15 minutes extra 2 minutes for the rest of processes
            'channel' => 'notifications', // Queue channel key,
        ],
        'redisMoviesQueue' => [
            'class' => \yii\queue\redis\Queue::class,
            'attempts' => 5, // Max number of attempts
            'ttr' => 1020,  // max TTL of process - since API call can take upto 15 minutes extra 2 minutes for the rest of processes
            'channel' => 'movies', // Queue channel key,
        ],
        'redisShowsQueue' => [
            'class' => \yii\queue\redis\Queue::class,
            'attempts' => 5, // Max number of attempts
            'ttr' => 1020,  // max TTL of process - since API call can take upto 15 minutes extra 2 minutes for the rest of processes
            'channel' => 'shows', // Queue channel key,
        ]
    ],
    'params' => [
        'traktApiKey' => env('TRAKT_API_KEY', '')
    ]
];
