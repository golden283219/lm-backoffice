<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\site\ShowsEpisodesReportsCache */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shows Episodes Reports Caches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-episodes-reports-cache-view">

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
            'count',
            'id_episode',
            'last_reported_at',
            'assigned_user_id',
            'is_closed',
        ],
    ]) ?>

</div>
