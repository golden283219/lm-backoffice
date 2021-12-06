<?php

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class TreeView extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@node_modules/bootstrap-treeview';
    /**
     * @var array
     */

    public $js = [
        'src/js/bootstrap-treeview.js'
    ];
    /**
     * @var array
     */
    public $css = [
        'src/css/bootstrap-treeview.css'
    ];
    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class,
    ];
}
