<?php

namespace backend\assets;

use yii\web\JqueryAsset;

class EpisodesDownloadQueueIndexAsset extends \yii\web\AssetBundle
{

	/**
	 * @var string
	 */
	public $sourcePath = "@backend/resources/js/moderation/episodes-download-queue";

	public $js = [
	    'index.js'
    ];

    /**
     * @var array
     */
    public $depends = [
        JqueryAsset::class
    ];
}
