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

class Axios extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/axios/dist';
    /**
     * @var array
     */
    public $js = [
        'axios.min.js'
    ];

}
