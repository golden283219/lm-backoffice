<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\site\ShowsEpisodesReportsCacheSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shows Episodes Reports Caches';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-episodes-reports-cache-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Shows Episodes Reports Cache', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'count',
            'id_episode',
            'last_reported_at',
            'assigned_user_id',
            // 'is_closed',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
