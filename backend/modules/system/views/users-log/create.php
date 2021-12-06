<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UsersActionLog */

$this->title = 'Create Users Action Log';
$this->params['breadcrumbs'][] = ['label' => 'Users Action Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-action-log-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
