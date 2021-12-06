<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesDownloadQueue */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Movies Download Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movies-download-queue-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'year',
            'imdb_id',
//            'url:url',
            'is_downloaded',
//            'source',
//            'bad_guids:ntext',
//            'bad_titles',
            'priority',
            'flag_quality',
            'worker_ip',
            'updated_at',
            'original_language',
            'type',
            'torrent_blob',
            'rel_title'
        ],
    ]) ?>

</div>
