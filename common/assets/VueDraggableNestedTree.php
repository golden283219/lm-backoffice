<?php

namespace common\assets;

use yii\web\AssetBundle;

class VueDraggableNestedTree extends AssetBundle
{

    public $sourcePath = '@base/node_modules/vue-draggable-nested-tree/dist';

    public $js = [
        'vue-draggable-nested-tree.js'
    ];

}
