<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 8:16 PM
 */

namespace common\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class Select2 extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/select2/dist';
    /**
     * @var array
     */
    public $js = [
        'js/select2.min.js'
    ];

    public $css = [
        'css/select2.min.css'
    ];

    public $depends = [
        JqueryAsset::class
    ];
}
