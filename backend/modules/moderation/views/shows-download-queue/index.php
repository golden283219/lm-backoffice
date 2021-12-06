<?php

use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\dynagrid\DynaGrid;

/* @var $this yii\web\View */
/* @var $searchModel common\models\queue\ShowsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shows';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    [
        'class' => '\kartik\grid\ExpandRowColumn',
        'enableRowClick' => true,
        'value' => function ($model, $key, $index) {
            return GridView::ROW_COLLAPSED;
        },
        'expandIcon' => '<i class="fa fa-plus-square-o" aria-hidden="true"></i>',
        'collapseIcon' => '<i class="fa fa-minus-square-o" aria-hidden="true"></i>',
        'detailUrl' => '/moderation/shows-download-queue/detail'
    ],
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    'title',
    'first_air_date',
    'imdb_id',
    'tvmaze_id',
    'original_language',
    'date_added',
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'dropdownMenu' => ['class' => 'menu-left'],
        'order' => DynaGrid::ORDER_FIX_RIGHT,
        'template' => implode('', [
            '{view}{view-episodes}{apply-magnet}{add-magnet-link}',
        ]),
        'buttons' => [
            'view' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-television" aria-hidden="true"></i> View TV Show',
                        $url
                    )
                );
            },
            'view-episodes' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-th-list" aria-hidden="true"></i> View Episodes',
                        '/moderation/episodes-download-queue?ShowsMetaSearch%5Bid_tvshow%5D=' . $model->id_tvshow
                    )
                );
            },
            'apply-magnet' => function ($url, $model) {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-map-signs" aria-hidden="true"></i> Apply Torrent',
                        $url
                    )
                );
            },
        ]
    ],
];

$toolbar = [
    '{export}',
    '{toggleData}',
    '{dynagrid}',
    [
        'content' =>
            Html::a(
                '<i class="glyphicon glyphicon-plus"></i> Add Show',
                '/moderation/shows-download-queue/add',
                [
                    'type' => 'button',
                    'id' => 'add-new-show',
                    'title' => 'Add TV Show',
                    'class' => 'btn btn-primary'
                ]
            ),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
];
?>
<div class="shows-index">
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
                'pjax' => false,
            ],
            'options' => ['id' => 'grid-shows-1', 'class' => 'expandable-row']
        ]);
    ?>
</div>

<script>

    window.addEventListener('DOMContentLoaded', function () {
        $('.map-with-torrent').on('click', function (event) {
            event.preventDefault();
            if (typeof (event.currentTarget.dataset.id) !== 'undefined') {
                window.dispatchEvent(new CustomEvent('handle-torrent-upload', {
                    detail: {
                        id_tvshow: event.currentTarget.dataset.id
                    }
                }));
            }
        });

        $('.add-magnet-link').on('click', function (event) {
            var id_tvshow = event.currentTarget.dataset.id;
            event.preventDefault();
            $('#add-magnet-link')
                .data('id_tvshow', id_tvshow)
                .modal('show');
        });

        $('#back-btn').on('click', function (event) {
            $('#episode-magnet-link').modal('hide');
            $('#add-magnet-link').modal('show');
        });

        $('#continue-btn').on('click', function (event) {
            magnet_link = $('#magnet_link').val();
            if (magnet_link.length > 0) {
                $('#add-magnet-link').modal('hide');
                if (typeof ($('#add-magnet-link').data('id_tvshow')) !== 'undefined') {
                    id_tvshow = $('#add-magnet-link').data('id_tvshow');
                    const options = {
                        headers: {
                            "X-CSRF-Token": Yii2.csrf
                        }
                    };
                    axios.post('/moderation/shows-download-queue/episodes', {idTvShow: id_tvshow}, options).then(function (response) {
                        console.log($('#episode-modal-content'));
                        $('#episode-modal-content').treeview({
                            showIcon: false,
                            showCheckbox: true,
                            data: response.data
                        })
                            .on('nodeChecked', function (e, node) {
                                if (typeof node['nodes'] != "undefined") {
                                    var children = node['nodes'];
                                    for (var i = 0; i < children.length; i++) {
                                        $('#episode-modal-content').treeview('checkNode', [children[i].nodeId, {silent: true}]);
                                    }
                                }
                            })
                            .on('nodeUnchecked', function (e, node) {
                                if (typeof node['nodes'] != "undefined") {
                                    var children = node['nodes'];
                                    for (var i = 0; i < children.length; i++) {
                                        $('#episode-modal-content').treeview('uncheckNode', [children[i].nodeId, {silent: true}]);
                                    }
                                }
                            });
                        $('#episode-modal-content').treeview('collapseAll', {silent: true});
                        $('#episode-magnet-link').modal('show');
                    });
                };
            } else {
                alert("Empty magnet link");
            }
        });

        $('#save-btn').on('click', function (event) {
            event.preventDefault();
            magnet_link = $('#magnet_link').val();
            nodes = $('#episode-modal-content').treeview('getChecked');
            selectedEpisodes = [];
            if (nodes) {
                $(nodes).each(function (index, value) {
                    if (typeof (value.id_meta) !== 'undefined') {
                        selectedEpisodes.push(value.id_meta);
                    }
                });

                if (selectedEpisodes.length > 0) {
                    const options = {
                        headers: {
                            "X-CSRF-Token": Yii2.csrf
                        }
                    };
                    axios.post('/moderation/shows-download-queue/update-episodes', {ids_meta: selectedEpisodes, torrent_blob: magnet_link}, options).then(function (response) {
                        $('#episode-magnet-link').modal('hide');
                    });
                }
            }
        });
    });
</script>
<style>
    #episode-modal-content .text_block {
        display: inline-flex;
        margin-left: 10px;
    }
    #episode-modal-content .text_block .name {
        width:490px;
    }
    #episode-modal-content .text_block div {
        margin: 0 10px;
    }
</style>
