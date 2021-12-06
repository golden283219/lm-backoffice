<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PremPlans */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="prem-plans-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?php echo $form->errorSummary($model); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 col-sm-12">
            <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3 col-sm-12">
            <?php echo $form->field($model, 'price_usd')->textInput() ?>
        </div>
        <div class="col-md-3 col-sm-12">
            <?php echo $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3 col-sm-12">
            <?php echo $form->field($model, 'extra_time')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <?php echo $form->field($model, 'month_count')->textInput() ?>
        </div>

        <div class="col-sm-3">
            <?php echo $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div><div class="col-sm-3"><?php echo $form->field($model, 'is_active')->textInput() ?></div>
        <div class="col-sm-3">
            <?php echo $form->field($model, 'is_default')->textInput() ?>
        </div>
        <div class="col-sm-3">
            <?php echo $form->field($model, 'is_active')->textInput() ?>
        </div>
    </div>

    <div class="form-group" style="text-align: right">
        <?php echo Html::submitButton('Save', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
