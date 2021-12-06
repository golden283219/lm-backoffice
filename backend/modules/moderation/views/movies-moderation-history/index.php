<?php

use yii\grid\GridView;
use backend\modules\moderation\models\MoviesDownloadQueue;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\moderation\models\MoviesModerationHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Movies History';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movies-moderation-history-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => 'Title',
                'format' => function ($model) {
                    return strtr('{$title} ({$year})', [
                        '{$title}' => $model->title,
                        '{$year}' => $model->year
                    ]);
                },
                'attribute' => 'title',
                'value' => function ($model) {
                    return $model;
                },
            ],
            [
                'label' => 'Release Title',
                'headerOptions' => ['style' => 'width:40%'],
                'format' => function ($model) {
                    $value = '(not set)';
                    if ($model->data !== null) {
                        try {
                            $dataObj = json_decode($model->data);
                            if (isset($dataObj->torrentTitle)) {
                                return base64_decode($dataObj->torrentTitle);
                            }
                        } catch (\Exception $e) {
                            \Yii::$app->getLog()->logger->log($e->getMessage(), LOG_ERR, 'MoviesModerationHistory');
                        }
                    }
                    return $value;
                },
                'value' => function ($model) {
                    return $model;
                }
            ],
             [
                 'attribute' => 'status',
                 'filter' => [
                      MoviesDownloadQueue::STATUS_DOWNLOADED => 'ON SITE',
                      MoviesDownloadQueue::STATUS_WAITING_USENET_DOWNLOADER => 'WAITING(usenet)',
                      MoviesDownloadQueue::STATUS_WAITING_TORRENT_DOWNLOADER => 'WAITING(torrent)',
                      MoviesDownloadQueue::STATUS_BEING_CONVERTED => 'BEING CONVERTED',
                      MoviesDownloadQueue::STATUS_MISSING_DOWNLOAD_CANDIDATE => 'Declined'
                 ],
                 'format' => function ($value) {
                     switch ($value) {
                         case 10:
                             $value = '<span class="badge badge-secondary">WAITING(usenet)</span>';
                             break;

                         case 1:
                             $value = '<span class="badge badge-success">Finished</span>';
                             break;

                         case 3:
                             $value = '<span class="badge badge-dark">BEING CONVERTED</span>';
                             break;

                         case 13:
                             $value = '<span class="badge badge-info">WAITING(torrent)</span>';
                             break;

                         case 14:
                             $value = '<span class="badge badge-danger">Declined</span>';
                             break;

                         default:
                             $value = '(not set)';
                             break;
                     }

                     return $value;
                 },
             ],
             [
                 'attribute' => 'created_at',
                 'format' => function ($value) {
                    return \Yii::$app->formatter->asRelativeTime($value);
                 },
             ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'width:125px'],
                'template' => '{view} {moderate} {restart}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return '<a class="btn bg-olive btn-flat btn-sm" title="View History Item" href=' . '/moderation/movies-moderation-history/view?id=' . $model->id . '><span class="glyphicon glyphicon-eye-open"></span></a>';
                    },
                    'moderate' => function ($url, $model) {
                        return '<a class="btn bg-navy btn-flat btn-sm" title="Moderate Movie" href=' . '/moderation/movies-download-queue/update?id=' . $model->id_movie . '><span class="glyphicon glyphicon-share"></span></a>';
                    },
                    'restart' => function ($url, $model) {
                        return '<a title="Restart History Item" class="btn bg-orange btn-flat btn-sm" href=' . '/moderation/movies-moderation-history/restart?id=' . $model->id . '><span class="glyphicon glyphicon-repeat"></span></a>';
                    },
                ],
            ],
        ],
    ]); ?>

</div>
