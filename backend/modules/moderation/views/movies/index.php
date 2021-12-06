<?php

use backend\assets\MoviesModerationAssets;
use common\models\MoviesSubtitles;
use common\models\User;
use kartik\dynagrid\DynaGrid;
use yii\helpers\Html;

MoviesModerationAssets::register($this);

$this->title = 'Site Movies';
$this->params['breadcrumbs'][] = $this->title;

$columns = [
    ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
    [
        'label' => 'Title',
        'attribute' => 'title',
        'format' => function ($model) {
            return $this->render('_m-card', [
                'model' => $model
            ]);
        },
        'value' => function ($model) {
            return $model;
        },
    ],
    [
        'attribute' => 'flag_quality',
        'label' => 'Quality',
        'filter' => [
            8 => 'FHD',
            7 => 'HD',
            0 => 'LQ'
        ],
        'format' => function ($value) {
            $formatted_value = '<span class="badge badge-danger">LQ</span>';

            if ($value == 7) {
                $formatted_value = '<span class="badge badge-info">HD - 720p</span>';
            }

            if ($value == 8) {
                $formatted_value = '<span class="badge badge-success">FHD - 1080p</span>';
            }

            return $formatted_value;

        }
    ],
    [
        'label' => 'Mode',
        'format' => function ($value) {
            switch ($value->moviesModeration->is_locked) {
                case '0':
                    $formatted_value = '<span class="badge badge-success">UNLOCKED</span>';
                    break;

                case '1':
                    $formatted_value = '<span class="badge badge-warning">LOCKED</span>';
                    break;

                case '2':
                    $user_name = !empty($value->moviesModeration->locked_by) ?
                        User::getAllIdentities()[$value->moviesModeration->locked_by]['username'] :
                        '';

                    $badge = $value->moviesModeration->locked_by == Yii::$app->user->identity->id ?
                        'badge-info' :
                        'badge-warning';

                    $formatted_value = "<span class='badge $badge'>DRAFT ($user_name)</span>";
                    break;

                case '3':
                    $formatted_value = '<span class="badge badge-dark">BEING CONVERTED</span>';
                    break;

                default:
                    $formatted_value = '(not set)';
                    break;
            }
            return $formatted_value;
        },
        'attribute' => 'is_locked',
        'filter' => [
            0 => 'UNLOCKED',
            1 => 'LOCKED',
            2 => 'DRAFT',
            3 => 'BEING CONVERTED',
        ],
        'value' => function ($model) {
            return $model;
        },
    ],
    [
        'attribute' => 'poster',
        'label' => 'Poster Status',
        'filter' => [
            'exists' => 'Exists',
            'missing' => 'Missing'
        ],
        'format' => function ($value) {
            $formatted_value = '<span class="badge badge-danger">Missing</span>';

            if (!empty($value)) {
                return '<span class="badge badge-success">Exists</span>';
            }

            return $formatted_value;
        },
    ],
    'date_added',
    [
        'label' => 'Is Enabled',
        'format' => function ($value) {
            $formatted_value = '(not set)';
            switch ($value) {
                case '0':
                    $formatted_value = '<span class="badge badge-danger">DISABLED</span>';
                    break;

                case '1':
                    $formatted_value = '<span class="badge badge-success">ENABLED</span>';
                    break;

                default:
                    $formatted_value = '(not set)';
                    break;
            }
            return $formatted_value;
        },
        'attribute' => 'is_active',
        'filter' => [
            0 => 'DISABLED',
            1 => 'ENABLED'
        ],
        'value' => function ($model) {
            return $model->is_active;
        },
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'order' => DynaGrid::ORDER_FIX_RIGHT,
        'options' => [
            'class' => 'menu-left'
        ],
        'template' => implode('', [
            '{update}{hr}{force-meta-download}',
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
            'force-meta-download' => function () {
                return Html::tag(
                    'li',
                    Html::a(
                        '<i class="fa fa-download" aria-hidden="true"></i> Force Meta Download',
                        '/',
                        ['data-pjax' => '0']
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
        'content' => Html::dropDownList(
            'bulk-actions-list',
            'Bulk Actions',
            [
                '' => '-- Bulk Actions --',
                'force-meta-download' => 'Force Meta Download',
            ],
            [
                'id' => 'bulk-action-list',
                'class' => 'form-control',
            ]
        ),
        'options' => [
            'class' => 'btn-group pull-right'
        ]
    ],
    [
        'content' => Html::a('Add New Movie', '/moderation/movies-download-queue/create', [
            'class'     => 'btn btn-primary',
            'data-pjax' => 0
        ]),
        'options' => [
            'class' => 'btn-group pull-right',
        ]
    ],
];

echo DynaGrid::widget([
    'columns' => $columns,
    'storage' => DynaGrid::TYPE_COOKIE,
    'theme' => 'panel-info',
    'allowPageSetting' => false,
    'gridOptions' => [
        'itemLabelSingle' => 'movie',
        'itemLabelPlural' => 'movies',
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

?>

<style>
    .dropdown-menu {
        right: 0;
        left: auto;
    }
</style>

<script>
    window.addEventListener('DOMContentLoaded', function () {
        MoviesModerationIndex();
    });
</script>
