<?php

namespace common\assets;

class MomentJs extends \yii\web\AssetBundle
{

    public $sourcePath = '@base/node_modules/moment/min';

    public $js = [
        'moment.min.js'
    ];

}
