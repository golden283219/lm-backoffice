<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PremPlansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Premium Membership Tariff Plans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prem-plans-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Add Plan', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'title',
            [
                'attribute' => 'price_usd',
                'label'     => 'Price, USD',
                'format'    => function ($value) {
                    return '$'.$value;
                }
            ],
//            'description',
//            'discount',
            // 'code',
            // 'extra_time:datetime',
            // 'is_default',
            'month_count',
            'is_active',
            // 'affiliate_tariff_maping',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
