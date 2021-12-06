<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\queue\YtConverters */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="yt-converters-form">

    <?php $form = ActiveForm::begin(['id'=> 'form-server-update-' . $model->id]); ?>

    <div class="row">
        <div class="col-md-6 col-sm-12"><?php echo $form->field($model, 'ip')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6 col-sm-12"><?php echo $form->field($model, 'server_name')->textInput(['maxlength' => true]) ?></div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-12"><?php echo $form->field($model, 'status_check_url')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6 col-sm-12">
            <?php
                echo $form->field($model, 'type')
                    ->label('Server Type')
                    ->dropDownList(
                        [0 => 'Shows', 1 => 'Movies'],
                        ['prompt'=>'']
                    );
            ?>
        </div>
    </div>

    <?php echo $form->errorSummary($model); ?>

    <div class="form-group" style="text-align: right;">
        <?php echo Html::submitButton($model->isNewRecord ? 'ADD' : 'Save', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
