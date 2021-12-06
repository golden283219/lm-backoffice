<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="prem-users-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation' => isset($enableAjaxValidation) && $enableAjaxValidation ? true : false,
    ]); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <div class="form-group field-premusers-status">
        <label class="control-label" for="premusers-status">Account Status</label>
        <?php echo Html::activeDropDownList($model, 'status', [
            1 => 'Active',
            0 => 'Inactive',
        ], ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
