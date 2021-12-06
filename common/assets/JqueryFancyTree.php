<?php

namespace common\assets;

use yii\web\AssetBundle;

class JqueryFancyTree extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/jquery.fancytree/dist';

    public $css = [
        'skin-win8/ui.fancytree.min.css'
    ];

    /**
     * @var array
     */
    public $js = [
        'jquery.fancytree-all-deps.min.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
