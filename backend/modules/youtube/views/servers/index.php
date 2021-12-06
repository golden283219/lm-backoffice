<?php

use backend\assets\ServersIndexAsset;
use backend\models\queue\YtConverters;
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;
use yii\bootstrap\Modal;

ServersIndexAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\queue\YtConvertersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Yt Converters';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'class' => '\kartik\grid\ExpandRowColumn',
        'enableRowClick' => true,
        'enableCache' => false,
        'value' => function ($model, $key, $index) {
            return GridView::ROW_COLLAPSED;
        },
        'expandIcon' => '<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
        'collapseIcon' => '<i class="fa fa-minus-square-o" aria-hidden="true"></i>',
        'detailUrl' => '/youtube/servers/details'
    ],
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    [
        'attribute' => 'server_name',
        'width'     => '120px'
    ],
    [
        'attribute' => 'ip',
        'width'     => '140px'
    ],
    [
        'attribute' => 'type',
        'label'     => 'Server Type',
        'width' => '165px',
        'format'    => function ($value){
            switch ($value) {
                case 0:
                    return '<div><i class="fa fa-television" aria-hidden="true"></i> <span class="shows">Shows Converter</span></div>';
                    break;
                case 1:
                    return '<div><i class="fa fa-film" aria-hidden="true"></i> <span class="movies">Movies</span></div>';
                    break;
            }

            return '(Not Set)';
        },
        'filter' => ['Shows', 'Movies']
    ],
    [
        'label' => 'Status',
        'attribute' => 'status_check_url',
        'format' => function ($value) {
            return "<div data-id='{$value->id}' data-status-url='{$value->status_check_url}'>
                Loading...
            </div>";
        },
        'value' => function ($model) {
            return $model;
        }
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'order' => DynaGrid::ORDER_FIX_RIGHT,
        'options' => [
            'class' => 'menu-left'
        ],
        'template' => implode('', [
            '{vnc-connect}{restart}{hr}{update}{delete}',
        ]),
        'buttons' => [
            'hr' => function ($url, $model) {
                return '<li class="divider"></li>';
            },
            'update' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-edit" aria-hidden="true"></i> Update..',
                        $url,
                        ['target' => "_blank", 'data-pjax' => '0', 'data-modal-edit' => '1']
                    )
                );
            },
            'vnc-connect' => function ($url, $model) {
                /**
                 * @var $model YtConverters
                 */
                $link = 'http://'.$model->get_clean_ip().':6080/vnc_auto.html';
                $title = $model->server_name.' ('.$model->get_clean_ip().') | VNC';
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-link" aria-hidden="true"></i> VNC Connect..',
                        "/youtube/vnc/link?title=$title&link=$link",
                        ['target' => "_blank", 'data-pjax' => '0']
                    )
                );
            },
            'restart' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-refresh" aria-hidden="true"></i> Restart..',
                        $url,
                        [
                            'target' => "_blank", 'data-pjax' => '0', 'data-restart-conversion' => '1',
                            'data-ip' => $model->ip, 'data-server-name' => $model->server_name
                        ]
                    )
                );
            },
        ]
    ],
    ['class' => 'kartik\grid\CheckboxColumn', 'order' => DynaGrid::ORDER_FIX_RIGHT],
];

$toolbar = [
    '{export}',
    '{toggleData}',
    '{dynagrid}',
    [
        'content' => Html::button('Apply', [
            'id' => 'apply-bulk-actions',
            'class' => 'btn btn-default'
        ]),
        'options' => [
            'class' => 'btn-group pull-right',
        ]
    ],
    [
        'content' => Html::dropDownList('bulk-actions-list', 'Bulk Actions', [
            '' => 'Bulk Actions',
            'bulk-restart' => 'Restart',
            'bulk-delete' => 'Delete',
        ],
            [
                'id' => 'bulk-action-list',
                'class' => 'form-control',
            ]),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
    [
        'content' =>
            Html::button('<i class="glyphicon glyphicon-refresh"></i> Reload Status', [
                'type' => 'button',
                'id' => 'reload-server-status',
                'title' => 'reload servers status',
                'class' => 'btn btn-default'
            ]),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
    [
        'content' =>
            Html::button('<i class="glyphicon glyphicon-plus"></i> Add Server', [
                'type' => 'button',
                'id' => 'add-new-server',
                'title' => 'Add Server',
                'class' => 'btn btn-primary'
            ]),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
];

echo DynaGrid::widget([
    'columns' => $columns,
    'storage' => DynaGrid::TYPE_COOKIE,
    'theme' => 'panel-info',
    'allowPageSetting' => false,
    'gridOptions' => [
        'itemLabelSingle' => 'converter',
        'itemLabelPlural' => 'converters',
        'showPageSummary' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'panel' => [
            'heading' => '<h3 class="panel-title">' . $this->title . '</h3>',
        ],
        'toolbar' => $toolbar,
        'hover' => true,
        'responsive' => false,
        'pjax' => true,
    ],
    'options' => ['id' => 'grid-yt-converters-1', 'class' => 'expandable-row']
]);

Modal::begin([
    'header' => '<span>ADD NEW CONVERTER</span>',
    'size' => Modal::SIZE_SMALL,
    'options' => [
        'id' => 'add-converter-modal'
    ]
]);

echo $this->render('_form', [
    'model' => $model,
    'enableAjaxValidation' => true
]);

Modal::end();

?>

<style>
    th[data-col-seq="2"],
    td[data-col-seq="2"],
    th[data-col-seq="3"],
    td[data-col-seq="3"],
    th[data-col-seq="4"],
    th[data-col-seq="5"],
    td[data-col-seq="4"],
    td[data-col-seq="5"] {
        text-align: center;
    }

    .dropdown-toggle + ul {
        left: auto;
        right: 0;
    }

    .kv-panel-after,
    .panel-footer {
        display: none !important;
    }
</style>
