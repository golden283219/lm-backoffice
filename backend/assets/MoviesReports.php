<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 8:16 PM
 */

namespace common\assets\AppAssets;

use yii\web\AssetBundle;

class MoviesReports extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@base/javascript_app';
    /**
     * @var array
     */
    public $js = [
        'MoviesReports.js'
    ];

}
