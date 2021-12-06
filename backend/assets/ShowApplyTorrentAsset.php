<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 7/3/14
 * Time: 8:16 PM
 */

namespace backend\assets;

use common\assets\VueDraggableNestedTree;
use common\assets\VueJs;
use yii\web\AssetBundle;

class ShowApplyTorrentAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@backend/resources/js/moderation/shows-download-queue';

    /**
     * @var array
     */
    public $js = [
        'apply-magnet.js'
    ];

    public $depends = [
        VueDraggableNestedTree::class,
        VueJs::class,
    ];
}
