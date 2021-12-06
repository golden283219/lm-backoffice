<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 8:16 PM
 */

namespace common\assets;

use yii\web\AssetBundle;

class VueJs extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/vue/dist';
    /**
     * @var array
     */
    public $js = [
        'vue.js'
    ];

}
