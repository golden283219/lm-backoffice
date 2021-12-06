<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UsersActionLog */

$this->title = 'Update Users Action Log: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users Action Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="users-action-log-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
