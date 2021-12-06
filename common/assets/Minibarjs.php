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

class Minibarjs extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/minibarjs/dist';
    /**
     * @var array
     */
    public $js = [
        'minibar.min.js'
    ];

    public $css = [
        'minibar.min.css'
    ];
    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class
    ];
}
