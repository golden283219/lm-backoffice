<?php

namespace backend\widgets\assets;

use common\assets\VueJs;
use yii\web\JqueryAsset;

class EpisodeApplyTorrentAssets extends \yii\web\AssetBundle
{

	/**
	 * @var string
	 */
	public $sourcePath = "@backend/widgets/resources/js";

	public $js = [
	    'episode-apply-torrent.js'
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class
    ];
}
