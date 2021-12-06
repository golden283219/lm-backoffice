<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\EpisodesModerationHistory */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Episodes Moderation Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="episodes-moderation-history-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_meta',
            'title',
            'imdb_id',
            'tvmaze_id',
            'air_date',
            'priority',
            'original_language',
            'id_user',
            'status',
            'data',
            'guid',
            'type',
            'worker_ip',
            'created_at',
            'updated_at',
            'is_deleted',
        ],
    ]) ?>

</div>
