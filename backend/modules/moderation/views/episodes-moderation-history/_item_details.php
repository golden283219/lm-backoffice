<?php

use backend\modules\moderation\models\EpisodesModerationHistory;
use yii\widgets\DetailView;

/**
 * @var $model EpisodesModerationHistory
 */
echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => 'TV Show',
            'format' => function ($model) {
                return "
                <div class='history-item'>
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

                    case 4:
                        $value = '<span class="badge badge-info">WAITING(torrent)</span>';
                        break;

                    case 5:
                        $value = '<span class="badge badge-danger">Declined</span>';
                        break;

                    default:
                        $value = '(not set)';
                        break;
                }
                return $value;
            },
        ],
        'worker_ip',
        'id_meta',
        [
            'label' => 'External IDs:',
            'attribute' => 'imdb_id',
            'value' => function ($model) {
                return $model;
            },
            'format' => function ($value) {
                return 'IMDb: ' . $value->imdb_id . ';  TVMaze: ' . $value->tvmaze_id;
            }
        ],
        'air_date',
        'priority',
        'original_language',
        [
            'label' => 'Release Title',
            'headerOptions' => ['style' => 'width:40%'],
            'format' => function ($model) {
                $value = '(not set)';
                if ($model->data !== null) {
                    try {
                        $dataObj = json_decode($model->data);
                        if (isset($dataObj->torrent_title)) {
                            return base64_decode($dataObj->torrent_title);
                        }
                    } catch (\Exception $e) {
                        \Yii::$app->getLog()->logger->log($e->getMessage(), LOG_ERR, 'EpisodesModerationHistory');
                    }
                }
                return $value;
            },
            'value' => function ($model) {
                return $model;
            }
        ],
        [
            'label' => 'Torrent:',
            'headerOptions' => ['style' => 'width:40%'],
            'format' => function ($model) {
                $value = '(not set)';
                if ($model->data !== null) {
                    try {
                        $dataObj = json_decode($model->data);
                        if (isset($dataObj->torrent_blob)) {
                            return base64_decode($dataObj->torrent_blob);
                        }
                    } catch (\Exception $e) {
                        \Yii::$app->getLog()->logger->log($e->getMessage(), LOG_ERR, 'EpisodesModerationHistory');
                    }
                }
                return $value;
            },
            'value' => function ($model) {
                return $model;
            }
        ],
    ],
]);