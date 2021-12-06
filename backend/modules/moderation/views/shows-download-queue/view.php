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
        <?php echo Html::a('<i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Go Back', \Yii::$app->request->getReferrer(), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('<i class="fa fa-circle-o" aria-hidden="true"></i> View All Episodes', '/moderation/episodes-download-queue?ShowsMetaSearch%5Bid_tvshow%5D=' . $model->id_tvshow, ['class' => 'btn btn-success bg-navy']) ?>
        <?php echo Html::a('Update', '/moderation/shows-download-queue/update?id=' . $model->id_tvshow, ['class' => 'btn btn-success']) ?>
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
