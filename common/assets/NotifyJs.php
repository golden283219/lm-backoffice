<?php

namespace common\assets;

use yii\web\AssetBundle;

class NotifyJs extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/notify-js-legacy';
    /**
     * @var array
     */
    public $js = [
        'notify.js'
    ];

}
