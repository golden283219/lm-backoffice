<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\FeServers */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fe Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fe-servers-view">

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
            'ip',
            'domain',
            'status_check_url:url',
            'max_bw',
            'is_enabled',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
