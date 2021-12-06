<?php

namespace backend\assets;

use common\assets\VueJs;
use common\assets\VueDraggableNestedTree;

class VueBundle extends \yii\web\AssetBundle
{
    /**
     * @var array
     */
    public $depends = [
        VueJs::class,
        VueDraggableNestedTree::class
    ];
}
