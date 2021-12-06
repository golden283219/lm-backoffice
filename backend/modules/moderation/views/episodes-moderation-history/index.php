<?php

/**
 * @var $dataProvider
 * @var $searchModel
 */

use backend\assets\EpisodesModerationHistoryAsset;
use backend\modules\moderation\models\EpisodesModerationHistory;
use backend\modules\moderation\models\EpisodesModerationHistorySearch;
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;

EpisodesModerationHistoryAsset::register($this);

$this->title = 'Episodes Moderation History';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    [
        'class' => '\kartik\grid\ExpandRowColumn',
        'enableRowClick' => true,
        'value' => function ($model, $key, $index) {
            return GridView::ROW_COLLAPSED;
        },
        'enableCache' => false,
        'expandIcon' => '<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
        'collapseIcon' => '<i class="fa fa-minus-square-o" aria-hidden="true"></i>',
        'detail' => function($model, $key, $index) {
            $searchModel = new EpisodesModerationHistorySearch();
            $dataProvider = $searchModel->search(['imdb_id' => $model->imdb_id], true);
            $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
            $dataProvider->query->andWhere(['imdb_id' => $model->imdb_id]);
            return \Yii::$app->controller->renderPartial('group_item', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    ],
    [
        'label' => 'TV Show',
        'headerOptions' => ['style' => 'width:30%'],
        'format' => function ($model) {
            return "
                <div class='history-item--shows'>
                    <span class='history-item__title'>{$model->title}</span>
                </div>
            ";
        },
        'attribute' => 'title',
        'value' => function ($model) {
            return $model;
        },
    ],
    [
        'attribute' => 'status',
        'filter' => [
            EpisodesModerationHistory::STATUS_DOWNLOADED => 'ON SITE',
            EpisodesModerationHistory::STATUS_WAITING_USENET_DOWNLOADER => 'WAITING(usenet)',
            EpisodesModerationHistory::STATUS_WAITING_TORRENT_DOWNLOADER => 'WAITING(torrent)',
            EpisodesModerationHistory::STATUS_BEING_CONVERTED => 'BEING CONVERTED',
            EpisodesModerationHistory::STATUS_MISSING_DOWNLOAD_CANDIDATE => 'Declined'
        ],
        'format' => function ($value) {
            return "";
        },
    ],
    [
        'attribute' => 'updated_at',
        'label' => 'Last Updated Time',
        'format' => function ($value) {
            return '';
        },
    ],
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
            'class' => 'btn-group pull-right'
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
];
?>

<div class="episodes-moderation-history-index">
    <?php
    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-info',
        'gridOptions' => [
            'itemLabelSingle' => 'item',
            'itemLabelPlural' => 'items',
            'showPageSummary' => false,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'panel' => [
                'heading' => '<h3 class="panel-title">' . $this->title . '</h3>',
            ],
            'toolbar' => $toolbar,
            'hover' => true,
            'responsive' => false,
            'pjax' => false,
            'export' => false,
        ],
        'options' => ['id' => 'grid-episodes-moderation-group', 'class' => 'expandable-row']
    ]);
    ?>
</div>

<style>
    .dropdown-toggle + ul {
        left: auto;
        right: 0;
    }
</style>
