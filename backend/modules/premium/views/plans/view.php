<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PremPlans */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Prem Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prem-plans-view">

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
            'price_usd',
            'title',
            'description',
            'discount',
            'code',
            'extra_time:datetime',
            'is_default',
            'is_active',
            'month_count',
            'affiliate_tariff_maping',
        ],
    ]) ?>

</div>
