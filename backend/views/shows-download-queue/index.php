<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\queue\ShowsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shows';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Shows', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_tvshow',
            'title',
            'first_air_date',
            'imdb_id',
            'tmdb_id',
            // 'tvmaze_id',
            // 'total_episodes',
            // 'total_seasons',
            // 'episode_duration',
            // 'in_production',
            // 'status',
            // 'date_added',
            // 'data:ntext',
            // 'original_language',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
