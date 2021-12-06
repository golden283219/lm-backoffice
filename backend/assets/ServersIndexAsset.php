<?php

namespace backend\assets;

class ServersIndexAsset extends \yii\web\AssetBundle
{

    /**
     * @var string
     */
    public $sourcePath = "@backend/resources/js/youtube/status-check";

    public function init()
    {
        parent::init();

        $this->js = [];

        $this->js[] = YII_ENV === 'dev' ? 'index.js?ts=' . time() : 'index.js';

    }
}
