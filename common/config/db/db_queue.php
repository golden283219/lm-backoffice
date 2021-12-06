<?php

return [
    'class' => yii\db\Connection::class,
    'dsn' => env('DB_QUEUE_DSN'),
    'username' => env('DB_QUEUE_USERNAME'),
    'password' => env('DB_QUEUE_PASSWORD'),
    'tablePrefix' => env('DB_QUEUE_TABLE_PREFIX'),
    'charset' => env('DB_QUEUE_CHARSET'),
    'enableSchemaCache' => YII_ENV_PROD,
];