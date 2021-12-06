<?php
return [
    'class' => 'yii\web\UrlManager',
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => false,
    'rules' => [
        // Api
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/article', 'only' => ['index', 'view', 'options']],
        ['class' => 'yii\rest\UrlRule', 'controller' => 'api/v1/shows', 'only' => ['search']],
        '/v1/imdb-datasets/<imdb_id>/details' => 'v1/imdb-datasets/details',
        '/v1/imdb-datasets/<imdb_id>/episodes' => 'v1/imdb-datasets/episodes',
    ]
];
