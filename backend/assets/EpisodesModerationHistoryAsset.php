<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 8:16 PM
 */

namespace backend\assets;

use yii\web\AssetBundle;

class EpisodesModerationHistoryAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@backend/resources/js/moderation/episodes';

    /**
     * @var array
     */
    public $js = [
        'moderation-history.js'
    ];

}
