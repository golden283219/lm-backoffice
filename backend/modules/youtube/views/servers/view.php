<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\queue\YtConverters */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Yt Converters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="yt-converters-view">

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
            'server_name',
            'status_check_url:url',
            'type',
        ],
    ]) ?>

</div>
