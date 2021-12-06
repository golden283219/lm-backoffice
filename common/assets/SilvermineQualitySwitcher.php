<?php

namespace common\assets;

class SilvermineQualitySwitcher extends \yii\web\AssetBundle
{

    public $sourcePath = '@base/node_modules/@silvermine/videojs-quality-selector/dist';

    public $js = [
        'js/silvermine-videojs-quality-selector.min.js'
    ];

    public $css = [
        'css/quality-selector.css'
    ];

}
