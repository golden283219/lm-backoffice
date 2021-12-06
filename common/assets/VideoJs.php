<?php

namespace common\assets;

class VideoJs extends \yii\web\AssetBundle
{

    public $sourcePath = '@base/node_modules/video.js/dist';

    public $js = [
        'video.min.js'
    ];

    public $css = [
        'video-js.min.css'
    ];

}
