<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FeServersSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="fe-servers-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'ip') ?>

    <?php echo $form->field($model, 'server_name') ?>

    <?php echo $form->field($model, 'status_check_url') ?>

    <?php echo $form->field($model, 'max_bw') ?>

    <?php // echo $form->field($model, 'is_enabled') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
