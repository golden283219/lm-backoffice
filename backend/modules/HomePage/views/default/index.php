<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\site\HomePageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Home Pages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="home-page-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Home Page', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'position',
            'title',
            [
                'attribute' => 'code',
                'format' => 'raw',
                'value' => function ($data) {
                    $code = "<span class='badge badge-info'>" . $data->code . "</span>";
                    return $code;
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($data) {
                    $code = "<span class='badge badge-success'>Active</span>";

					if(!$data->is_active){
						$code = "<span class='badge badge-danger'>Inactive<span>";
					}
                    return $code;
                },
            ],
            [
                'attribute' => 'for_premium_user',
                'format' => 'raw',
                'value' => function ($data) {
                    $code = "<span class='badge badge-info'>No</span>";
					if($data->for_premium_user){
						$code = "<span class='badge badge-warning'>Yes</span>";
					}
                    return $code;
                },
            ],
            'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

