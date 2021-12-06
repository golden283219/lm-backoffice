<?php

use yii\bootstrap\ButtonDropdown;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use backend\assets\StreamingServersAsset;

StreamingServersAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\FeServersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Streaming Servers';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    'server_name',
    'ip',
    'max_bw',
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'is_enabled',
        'vAlign' => 'middle',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'order' => DynaGrid::ORDER_FIX_RIGHT
    ],
    ['class' => 'kartik\grid\CheckboxColumn', 'order' => DynaGrid::ORDER_FIX_RIGHT],
];
?>
<div class="fe-servers-index">

    <?php
        Modal::begin([
            'header' => 'ADD STREAMING SERVER',
            'size' => Modal::SIZE_SMALL,
            'options' => [
                'id' => 'add-server-modal'
            ]
        ]);

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
                'heading' => '<h3 class="panel-title">Streaming Servers</h3>',
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
                    'content' => Html::a('DUMP SERVERS', ['dump-servers'], ['class' => 'btn btn-warning']),
                    'options' => [
                        'class' => 'btn-group pull-right'
                    ]
                ],
                [
                    'content' =>
                        Html::button('<i class="glyphicon glyphicon-plus"></i> Add Server', [
                            'type'  => 'button',
                            'id'    => 'add-streaming-server',
                            'title' => 'Add Server',
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
