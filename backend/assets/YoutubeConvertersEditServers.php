<?php

namespace backend\assets;

class YoutubeConvertersEditServers extends \yii\web\AssetBundle
{

	/**
	 * @var string
	 */
	public $sourcePath = "@backend/resources/js/youtube/edit-servers";

	public function init()
	{
		parent::init();

		$this->js = [];

		$this->js[] = YII_ENV === 'dev' ? 'index.js?ts=' . time() : 'index.js';
	}

    /**
     * @var array
     */
    public $depends = [
        VueBundle::class
    ];
}
