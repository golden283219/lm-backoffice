<?php

namespace backend\assets;

use yii\web\AssetBundle;

class FlagsAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@backend/resources/css/flags';

    /**
     * @var array
     */
    public $css = [
        'flags.min.css'
    ];

}
