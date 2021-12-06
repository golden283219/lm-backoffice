<?php

/**
 * @var $dataProvider
 * @var $searchModel
 */


use backend\assets\EpisodesModerationHistoryAsset;
use backend\modules\moderation\models\EpisodesModerationHistory;
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;

EpisodesModerationHistoryAsset::register($this);

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
        'detailUrl' => '/moderation/episodes-moderation-history/detail'
    ],
    [
        'label' => 'TV Show',
        'headerOptions' => ['style' => 'width:30%'],
        'format' => function ($model) {
            return "
                <div class='history-item--shows'>
                    <span class='history-item__title'>{$model->title}</span>
                    <span class='history-item__ep-number'>" . $model->get_episode_season() . "</span>
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
        'format' => function ($value) {
            return EpisodesModerationHistory::get_status_formatted_message($value);
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'order' => DynaGrid::ORDER_FIX_RIGHT,
        'template' => implode('', [
            '<li>{view-imdb}</li>',
            '<li class="divider"></li>',
            '<li>{restart}</li>',
            '<li>{delete}</li>',
        ]),
        'buttons' => [
            'view-imdb' => function ($url, $model) {
                return Html::a(
                    '<i class="fa fa-imdb" aria-hidden="true"></i> View imdb: ' . $model->imdb_id,
                    "https://imdb.com/title/{$model->imdb_id}",
                    ['data-pjax' => 1, 'target' => '_blank']
                );
            },
            'restart' => function ($url, $model) {
                return Html::a(
                    '<i class="fa fa-refresh" aria-hidden="true"></i> Restart',
                    $url,
                    ['data-restart-history-item' => $model->id]
                );
            },
            'delete' => function ($url, $model) {
                return Html::a(
                    '<i class="fa fa-trash-o" aria-hidden="true"></i> Delete',
                    $url,
                    ['data-delete-history-item' => $model->id]
                );
            }
        ]
    ],
    [
        'attribute' => 'updated_at',
        'format' => function ($value) {
            return \Yii::$app->formatter->asRelativeTime($value);
        },
    ],
    ['class' => 'kartik\grid\CheckboxColumn', 'order' => DynaGrid::ORDER_FIX_RIGHT],
];
?>

<div class="episodes-moderation-history-group">
    <?php
    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-info',
        'gridOptions' => [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'hover' => true,
            'responsive' => false,
            'pjax' => true,
            'export' => false,
            'toolbar' => false,
            'showHeader'=> false,
            'showFooter'=> false,
            'summary' => '',
        ],
        'options' => ['id' => 'grid-episodes-moderation-details', 'class' => 'expandable-row'],
    ]);
    ?>
</div>