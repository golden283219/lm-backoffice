<?php

use yii\grid\GridView;
use common\helpers\Html;

$this->title = 'Episodes Reports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-episodes-reports-cache-index">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => 'Show Title',
                'value' => function ($model) {
                    return $model->show->title . ' (' . $model->show->year . ')';
                }
            ],
            'season_number',
            'episode_number',
            [
                'attribute' => 'last_reported_at',
                'label' => 'Last Reported At',
                'format' => 'html',
                'value' => function ($model) {
                    return '<i class="fa fa-clock-o"></i> ' . Yii::$app->formatter->asRelativeTime($model->last_reported_at);
                },
            ],
            [
                'label'     => 'Reports Count',
                'attribute' => 'count',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{combined}',
                'buttons' => [
                    'combined' => function ($url, $model) {
                        return Html::dropdownWithButton([
                            'text' => 'View',
                            'href' => '/moderation/episodes-reports/view?id_episode='.$model->id_episode,
                            'options' => [
                                'class' => 'btn btn-default dropdown-toggle',
                                'type'  => 'button',
                            ],
                            'items' => [
                                [
                                    'text' => 'Close All Tickets',
                                    'visible' => true,
                                    'options' => [
                                        'href' => '/moderation/episodes-reports/close-all?id_episode='.$model->id_episode
                                    ]
                                ],
                            ],
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
</div>

<style>
    .filters {
        display: none;
    }
</style>
