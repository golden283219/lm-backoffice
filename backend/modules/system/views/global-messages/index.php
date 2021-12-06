<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\dynagrid\DynaGrid;
use yii\bootstrap\Modal;
use backend\assets\GlobalMessagesAsset;

GlobalMessagesAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\GlobalMessagesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Global Messages';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'class' => 'kartik\grid\SerialColumn', 
        'order' => DynaGrid::ORDER_FIX_LEFT
    ],
    'title',
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'is_active',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'content',
        'format' => 'html'
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'order' => DynaGrid::ORDER_FIX_RIGHT
    ],
    [
        'class' => 'kartik\grid\CheckboxColumn', 
        'order' => DynaGrid::ORDER_FIX_RIGHT
    ],
];
?>
<div class="global-messages-index">
    <?php
        Modal::begin([
            'header' => 'ADD GLOBAL MESSAGE',
            'size' => Modal::SIZE_SMALL,
            'options' => [
                'id' => 'add-message-modal'
            ]
        ]);

        $model->date_start = date('Y-m-d');
        $model->date_end = date('Y-m-d');

        echo $this->render('_form', [
            'model' => $model,
            'enableAjaxValidation' => true
        ]);

        Modal::end();
    ?>

    <?php

    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-info',
        'gridOptions' => [
            'showPageSummary' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">Global Messages</h3>',
            ],
            'toolbar' => [
                '{export}',
                '{toggleData}',
                '{dynagrid}',
                [
                    'content' => Html::button('Apply', [
                        'id'       => 'apply-bulk-actions',
                        'class'    => 'btn btn-default'
                    ]),
                    'options' => [
                        'class' => 'btn-group pull-right'
                    ]
                ],
                [
                    'content' => Html::dropDownList('bulk-actions-list', 'Bulk Actions', [
                        ''        => 'Bulk Actions',
                        'bulk-enable'  => 'Enable',
                        'bulk-disable' => 'Disable',
                        'bulk-delete'  => 'Delete',
                    ],
                        [
                            'id'    => 'bulk-action-list',
                            'class' => 'form-control',
                        ]),
                    'options' => [
                        'class' => 'btn-group pull-right'
                    ]
                ],
                [
                    'content' =>
                        Html::button('<i class="glyphicon glyphicon-plus"></i> Add Message', [
                            'type'  => 'button',
                            'id'    => 'add-global-message',
                            'title' => 'Add Message',
                            'class' => 'btn btn-success'
                        ]),
                    'options' => [
                        'class' => 'btn-group pull-right'
                    ]
                ],
            ],
            'hover' => true,
            'responsive' => false,
            'pjax' => true,
        ],
        'options' => ['id' => 'grid-fe-servers-1'] // a unique identifier is important
    ]);
    ?>
</div>
