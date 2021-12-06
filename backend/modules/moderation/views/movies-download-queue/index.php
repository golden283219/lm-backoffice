<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\modules\moderation\models\MoviesDownloadQueue;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\moderation\models\MoviesDownloadQueueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Movies Download Queue';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movies-download-queue-index">

  <p>
    <?php echo Html::a('Add Movie', ['create'], ['class' => 'btn btn-primary']) ?>
  </p>

  <?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
      'title',
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
      'year',
      [
        'attribute' => 'imdb_id',
        'format' => function ($imdb_id) {
          return '<a href="https://imdb.com/title/tt' . $imdb_id . '" target="_blank">tt' . $imdb_id . '</a>';
        }
      ],
      'original_language',
      [
        'attribute' => 'is_downloaded',
        'value' => function ($model) {
          return $model->is_downloaded;
        },
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
            $value = '<span class="badge badge-danger">NO CANDIDATE</span>';
            break;

            default:
            $value = '(not set)';
            break;
          }

          return $value;
        },
        'filter' => [
          MoviesDownloadQueue::STATUS_DOWNLOADED => 'ON SITE',
          MoviesDownloadQueue::STATUS_WAITING_USENET_DOWNLOADER => 'WAITING(usenet)',
          MoviesDownloadQueue::STATUS_WAITING_TORRENT_DOWNLOADER => 'WAITING(torrent)',
          MoviesDownloadQueue::STATUS_BEING_CONVERTED => 'BEING CONVERTED',
          MoviesDownloadQueue::STATUS_MISSING_DOWNLOAD_CANDIDATE => 'NO CANDIDATE'
        ],
      ],
      [
        'class' => 'yii\grid\ActionColumn',
        'template' => '{moderate} {view} {update}',
        'buttons' => [
          'moderate' => function ($url, $model) {
            return '<a title="Moderate Movie" href=' . '/moderation/movies/update?id=' . $model->id . '><span class="glyphicon glyphicon-share"></span></a>';
          }
        ],
      ],
    ],
  ]); ?>

</div>
