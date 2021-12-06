<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 3:14 PM
 */

namespace backend\assets;

use common\assets\VideoJs;
use common\assets\SilvermineQualitySwitcher;

class VideoPlaybackAsset extends \yii\web\AssetBundle
{

    /**
     * @var string
     */
    public $basePath = '@webroot';
    /**
     * @var string
     */
    public $baseUrl = '@web';

    /**
     * @var array
     */
    public $depends = [
        VideoJs::class,
        SilvermineQualitySwitcher::class
    ];
}
