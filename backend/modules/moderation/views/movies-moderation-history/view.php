<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesModerationHistory */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Movies Moderation History', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php echo Html::a('<i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Go Back', \Yii::$app->request->getReferrer(), ['class' => 'btn btn-primary']) ?>
</p>

<div class="movies-moderation-history-view">
    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_movie',
            'title',
            'imdb_id',
            'year',
            'priority',
            'original_language',
            'id_user',
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
                'label' => 'Torrent Title',
                'attribute' => 'data',
                'format' => function ($data) {
                    if ($data !== null) {
                        try {
                            $dataObj = json_decode($data);
                            if (isset($dataObj->torrentTitle)) {
                                return '<div style="max-width: 100%; overflow: hidden;word-break: break-all;">' . base64_decode($dataObj->torrentTitle) . '</div>';
                            }
                        } catch (\Exception $e) {
                            \Yii::$app->getLog()->logger->log($e->getMessage(), LOG_ERR, 'MoviesModerationHistory');
                        }
                    }

                    return '(not set)';
                }
            ],
            [
                'label' => 'Torrent Blob',
                'attribute' => 'data',
                'format' => function ($data) {
                    if ($data !== null) {
                        try {
                            $dataObj = json_decode($data);
                            if (isset($dataObj->torrentBlob)) {
                                return '<div style="max-width: 100%; overflow: hidden;word-break: break-all;">' . base64_decode($dataObj->torrentBlob) . '</div>';
                            }
                        } catch (\Exception $e) {
                            \Yii::$app->getLog()->logger->log($e->getMessage(), LOG_ERR, 'MoviesModerationHistory');
                        }
                    }

                    return '(not set)';
                }
            ],
            'guid',
            'type',
            'worker_ip',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
