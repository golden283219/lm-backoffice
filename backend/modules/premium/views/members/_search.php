<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PremUsersSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="prem-users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'username') ?>

    <?php echo $form->field($model, 'auth_key') ?>

    <?php echo $form->field($model, 'password_hash') ?>

    <?php echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'last_login') ?>

    <?php // echo $form->field($model, 'avatar_path') ?>

    <?php // echo $form->field($model, 'cancel_timestamp') ?>

    <?php // echo $form->field($model, 'role') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'plain_password') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
