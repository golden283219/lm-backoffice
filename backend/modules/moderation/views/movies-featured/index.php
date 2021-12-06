<?php

use yii\grid\GridView;
use yii\bootstrap\Dropdown;
use common\models\MoviesSubtitles;
use yii\helpers\Html;

$this->title = 'Featured Movies';
$this->params['breadcrumbs'][] = $this->title;

$GLOBALS['back_url'] = urlencode($currentUrl);

?>

<div class="movies-index">
    <p>
        <?php echo Html::a('Add Movie', ['/moderation/movies-featured/create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'Title',
                'format' => function ($model) {
                    return $this->render('_m-card', [
                        'model' => $model,
                        'subs_count' => MoviesSubtitles::find()->where(['id_movie' => $model->id_movie])->count()
                    ]);
                },
                'headerOptions' => ['style' => 'width:30%'],
                'attribute' => 'title',
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
                        $formatted_value = '<span class="badge badge-success">HD - 720p</span>';
                    }

                    if ($value == 8) {
                        $formatted_value = '<span class="badge badge-success">FHD - 1080p</span>';
                    }

                    return $formatted_value;

                }
            ],
            [
                'attribute' => 'position',
                'value' => 'moviesFeatured.position',
                'contentOptions'=>['style'=>'width: 70px;'],
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{short-actions}',
                'buttons' => [
                    'short-actions' => function ($url, $model) {
                        return '<div class="btn-group left-menu">' .
                            '<a type="button" class="btn btn-info btn-flat btn-sm" href="/moderation/movies-featured/update?id=' . $model->moviesFeatured->id . '">' .
                            'Update' .
                            '</a>' .
                            '<a type="button" class="btn btn-info btn-flat btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
                            '<span class="caret"></span>' .
                            '</a>' .
                            Dropdown::widget([
                                'items' => [
                                    ['label' => 'View', 'url' => '/moderation/movies-featured/view?id=' . $model->moviesFeatured->id],
                                    [
                                        'label' => 'Delete From List',
                                        'url' => '/moderation/movies-featured/delete?id=' . $model->moviesFeatured->id,
                                        'linkOptions' => [
                                            'data' => [
                                                'confirm' => 'Are you sure ?',
                                                'method' => 'post',
                                            ],
                                        ],
                                    ],
                                ],
                            ]) .
                            '</div>';
                    }
                ],
            ],
        ],
    ]); ?>
</div>
