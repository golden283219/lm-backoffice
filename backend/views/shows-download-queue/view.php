<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\queue\Shows */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id_tvshow], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id_tvshow], [
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
            'id_tvshow',
            'title',
            'first_air_date',
            'imdb_id',
            'tmdb_id',
            'tvmaze_id',
            'total_episodes',
            'total_seasons',
            'episode_duration',
            'in_production',
            'status',
            'date_added',
            'data:ntext',
            'original_language',
        ],
    ]) ?>

</div>
