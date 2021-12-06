<?php

namespace backend\assets;

use common\assets\VideoJs;

class MoviesModerationAssets extends \yii\web\AssetBundle
{
	/**
	 * @var string
	 */
	public $sourcePath = "@backend/resources/js/moderation/movies";

    /**
     * @var array
     */
	public $js = [
	    'update.js',
        'index.js'
    ];

    /**
     * @var array
     */
    public $depends = [
        VueBundle::class,
        VideoJs::class
    ];
}
