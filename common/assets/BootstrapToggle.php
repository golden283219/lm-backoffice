<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 8:16 PM
 */

namespace common\assets;

use yii\web\AssetBundle;

class BootstrapToggle extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/node_modules/bootstrap4-toggle';
    /**
     * @var array
     */
    public $js = [
        'js/bootstrap4-toggle.min.js'
    ];

    public $css = [
        'css/bootstrap4-toggle.min.css'
    ];

}
