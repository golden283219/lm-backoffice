<?php
$config = [
    'homeUrl' => Yii::getAlias('@backendUrl'),
    'controllerNamespace' => 'backend\controllers',
    'defaultRoute' => 'moderation/movies',
    'components' => [
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'request' => [
            'cookieValidationKey' => env('BACKEND_COOKIE_VALIDATION_KEY'),
            'baseUrl' => env('BACKEND_BASE_URL'),
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],

        'user' => [
            'class' => yii\web\User::class,
            'identityClass' => common\models\User::class,
            'loginUrl' => ['sign-in/login'],
            'enableAutoLogin' => true,
            'as afterLogin' => common\behaviors\LoginTimestampBehavior::class,
        ],
    ],
    'modules' => [
        'dynagrid'=>[
            'class'=>'\kartik\dynagrid\Module',
            'defaultPageSize' => 20,
        ],
        'gridview'=>[
            'class'=>'\kartik\grid\Module',
            // other module settings
        ],
        'premium' => [
            'class' => 'backend\modules\premium\Module',
        ],
        'moderation' => [
            'class' => backend\modules\moderation\Module::class,
        ],
        'system' => [
            'class' => backend\modules\system\Module::class,
        ],
        'rbac' => [
            'class' => backend\modules\rbac\Module::class,
            'defaultRoute' => 'rbac-auth-item/index',
        ],
        'youtube' => [
            'class' => backend\modules\youtube\Module::class,
        ],
        'StaticPages' => [
            'class' => 'backend\modules\StaticPages\Module',
        ],
        'HomePage' => [
            'class' => 'backend\modules\HomePage\Module',
        ],
    ],
    'as globalAccess' => [
        'class' => common\behaviors\GlobalAccessBehavior::class,
        'rules' => [
            [
                'controllers' => ['sign-in'],
                'allow' => true,
                'actions' => ['login'],
            ],
            [
                'controllers' => ['sign-in'],
                'allow' => true,
                'roles' => ['@'],
                'actions' => ['logout'],
            ],
            [
                'controllers' => ['site'],
                'allow' => true,
                'roles' => ['?', '@'],
                'actions' => ['error'],
            ],
            [
                'controllers' => ['debug/default'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
              'controllers' => ['user'],
              'allow' => true,
              'roles' => ['administrator'],
            ],
            [
              'controllers' => [
                'system/global-messages',
                'system/key-storage'
              ],
              'allow' => true,
              'roles' => ['administrator'],
            ],
            [
                'controllers' => ['user', 'system'],
                'allow' => false,
            ],
            [
                'allow' => true,
                'roles' => ['moderator', 'super_moderator', 'administrator'],
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
        'generators' => [
            'crud' => [
                'class' => yii\gii\generators\crud\Generator::class,
                'templates' => [
                    'yii2-starter-kit' => Yii::getAlias('@backend/views/_gii/templates'),
                ],
                'template' => 'yii2-starter-kit',
                'messageCategory' => 'backend',
            ],
        ],
    ];
}

return $config;
