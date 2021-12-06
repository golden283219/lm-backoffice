<?php

return [
    'class' => yii\db\Connection::class,
    'dsn' => env('DB_IMDB_DSN'),
    'username' => env('DB_IMDB_USERNAME'),
    'password' => env('DB_IMDB_PASSWORD'),
    'tablePrefix' => env('DB_IMDB_TABLE_PREFIX'),
    'charset' => env('DB_IMDB_CHARSET', 'utf8'),
    'enableSchemaCache' => YII_ENV_PROD
];