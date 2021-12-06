<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 8:16 PM
 */

namespace common\assets;

use yii\web\AssetBundle;

class JqueryAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/jquery/dist';
    /**
     * @var array
     */
    public $js = [
        'jquery.min.js'
    ];

}
