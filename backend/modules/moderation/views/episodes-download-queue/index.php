<?php

use backend\widgets\EpisodeApplyTorrent;
use backend\assets\EpisodesDownloadQueueIndexAsset;
use backend\assets\EpisodesTorrentReconvert;
use kartik\dynagrid\DynaGrid;
use common\helpers\Html;

$this->title = 'Episodes Download Queue';
$this->params['breadcrumbs'][] = $this->title;

EpisodesTorrentReconvert::register($this);
EpisodesDownloadQueueIndexAsset::register($this);

$columns = [
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    [
        'label' => 'TV Show',
        'format' => function ($model) {
            return $this->render('_e-card', ['model' => $model]);
        },
        'attribute' => 'id_tvshow',
        'headerOptions' => ['style' => 'width:25%'],
        'value' => function ($model) {
            return $model;
        },
    ],
    [
        'attribute' => 'season',
        'headerOptions' => ['style' => 'width:80px'],
    ],
    [
        'attribute' => 'episode',
        'headerOptions' => ['style' => 'width:80px'],
    ],
    [
        'attribute' => 'state',
        'headerOptions' => ['style' => 'width:17%'],
        'format' => function ($value) {
            switch ($value) {
                case 1:
                    $value = '<span class="badge badge-success">ON SITE</span>';
                    break;

                case 3:
                    $value = '<span class="badge badge-dark">BEING CONVERTED</span>';
                    break;

                case 4:
                    $value = '<span class="badge badge-info">WAITING(torrent)</span>';
                    break;

                case 5:
                    $value = '<span class="badge badge-danger">NO CANDIDATE</span>';
                    break;

                default:
                    $value = '(not set)';
                    break;
            }

            return $value;
        },
        'value' => function ($model) {
            return $model->state;
        },
    ],
    [
        'headerOptions' => ['style' => 'width:20%'],
        'label' => 'Magnet Link',
        'attribute' => 'torrent_blob',
        'value' => function ($model) {
            if (strlen($model->torrent_blob) > 25) {
                return substr($model->torrent_blob, 0, 25) . '...';
            }
            return $model->torrent_blob;
        },
    ],
    [
        'attribute' => 'air_date'
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'dropdownMenu' => ['class' => 'menu-left'],
        'order' => DynaGrid::ORDER_FIX_RIGHT,
        'template' => '{update}{view}{delete}{divider}{add-magnet-link}',
        'buttons' => [
            'delete' => function ($url, $model) {
                if (!Yii::$app->user->can('administrator')) {
                    return '';
                }
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-trash" aria-hidden="true"></i> Delete',
                        $url,
                        ['data-pjax' => 0, 'data-ajax-method' => 'delete', 'data-id' => $model->id_meta]
                    )
                );
            },
            'divider' => function () {
                return Html::tag(
                    'li',
                    '',
                     ['class' => 'divider']
                );
            },
            'view' => function ($url, $model) {
                if (!Yii::$app->user->can('administrator')) {
                    return '';
                }

                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-television" aria-hidden="true"></i> View',
                        $url,
                        ['data-pjax' => 0]
                    )
                );
            },
            'add-magnet-link' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-link" aria-hidden="true"></i> Apply Magnet Link',
                        $url,
                        [
                            'class' => 'add-magnet-link',
                            'data-id' => $model->id_meta,
                            'data-pjax' => 0,
                            'data-season' => $model->season,
                            'data-episode' => $model->episode,
                            'data-title' => !empty($model->show->title) ? addslashes($model->show->title) : 'null',
                            'data-year' => !empty($model->show->first_air_date) ? $model->show->first_air_date : '0000-00-00',
                        ]
                    )
                );
            },
        ],
    ],
    ['class' => 'kartik\grid\CheckboxColumn', 'order' => DynaGrid::ORDER_FIX_RIGHT],
];

$toolbar = [
    '{export}',
    '{dynagrid}',
    [
        'content' => Html::dropdownWithButton([
            'text'    => 'Apply Magnet',
            'href'    => '#',
            'options' => [
                'class' => 'btn btn-default dropdown-toggle',
                'type'  => 'button',
                'data-bulk-action' => 1,
                'data-action' => 'apply-magnet'
            ],
            'items' => [
                [
                    'text' => 'Delete',
                    'visible' => Yii::$app->user->can('administrator'),
                    'options' => [
                        'href' => '#',
                        'data-bulk-action' => 1,
                        'data-action' => 'delete'
                    ],
                ],
            ],
        ]),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
    [
        'content' => Html::input('number', 'priority', 101, [
            'class' => 'form-control',
            'id' => 'input-priority'
        ]),
        'options' => [
            'class' => 'pull-right'
        ]
    ],
    [
        'content' =>
            Html::a(
                'Map Magnet to TV Show',
                 !empty($dataProvider->models[0]) ? '/moderation/shows-download-queue/apply-magnet?id=' . $dataProvider->models[0]->id_tvshow : '',
                [
                    'type'          => 'button',
                    'id'            => 'map-magnet-to-show',
                    'title'         => 'Add TV Show',
                    'class'         => 'btn btn-success',
                    'data-pjax'     => 0,
                    'data-base-url' => '/moderation/shows-download-queue/apply-magnet'
                ]
            ),
        'options' => [
            'class' => 'btn-group'
        ]
    ],
    [
        'content' =>
            Html::a(
                '<i class="glyphicon glyphicon-plus"></i> Add Show',
                '/moderation/shows-download-queue/add',
                [
                    'type'      => 'button',
                    'id'        => 'add-new-show',
                    'title'     => 'Add TV Show',
                    'class'     => 'btn btn-primary',
                    'data-pjax' => 0
                ]
            ),
        'options' => [
            'class' => 'btn-group'
        ]
    ],
];
if(!empty($dataProvider->models[0]) && !empty($dataProvider->models[0]->show)) {
    $year = isset($dataProvider->models[0]->show->first_air_date) && isset(explode('-', $dataProvider->models[0]->show->first_air_date)['0']) ? explode('-', $dataProvider->models[0]->show->first_air_date)['0'] : '0000';
    $script_command = '<script>window.showTitle = "'.addslashes($dataProvider->models[0]->show->title).'";' .
    'window.showImdbId = "' . $dataProvider->models[0]->show->imdb_id . '";' .
    'window.showYear = "'.$year.'";' .
    '</script>';
    $toolbar[] = [
        'content' => $script_command
    ];
}
?>

<div class="shows-meta-index">
    <?php
    echo DynaGrid::widget([
        'columns' => $columns,
        'storage' => DynaGrid::TYPE_COOKIE,
        'theme' => 'panel-info',
        'gridOptions' => [
            'itemLabelSingle' => 'show',
            'itemLabelPlural' => 'shows',
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
        'options' => ['id' => 'grid-shows-1', 'class' => 'expandable-row']
    ]);
    ?>
</div>

<?php echo EpisodeApplyTorrent::widget(); ?>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Apply Magnet</h4>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="6" name="bulk-apply-magnet-input"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="bulk-submit-magnet-link">Apply</button>
            </div>
        </div>
    </div>
</div>
