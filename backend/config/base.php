<?php
return [
    'id' => 'backoffice',
    'basePath' => dirname(__DIR__),
    'components' => [
        'urlManager' => require __DIR__ . '/_urlManager.php'
    ],
];
