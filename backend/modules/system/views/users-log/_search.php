<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UsersActionLogSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="users-action-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'id_user') ?>

    <?php echo $form->field($model, 'action') ?>

    <?php echo $form->field($model, 'category') ?>

    <?php echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'log_time') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
