<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UsersActionLog */

$this->title = 'Action #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users Action Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-action-log-view">

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_user',
            'action',
            'category',
            'data:ntext',
            'log_time:datetime',
        ],
    ]) ?>

</div>
