<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\queue\Shows */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="shows-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-12">
            <?php echo $form->errorSummary($model); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-6">
            <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?php echo $form->field($model, 'original_language')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?php echo $form->field($model, 'first_air_date')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-3">
            <?php echo $form->field($model, 'imdb_id')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?php echo $form->field($model, 'tmdb_id')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?php echo $form->field($model, 'tvmaze_id')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-12 col-md-3">
            <?php echo $form->field($model, 'status')->textInput() ?>
        </div>
    </div>

    <?php echo $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group pull-right">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Save', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <div style="display: none;">
        <?php echo $form->field($model, 'total_episodes')->textInput() ?>

        <?php echo $form->field($model, 'total_seasons')->textInput() ?>

        <?php echo $form->field($model, 'episode_duration')->textInput() ?>

        <?php echo $form->field($model, 'in_production')->textInput() ?>

        <?php echo $form->field($model, 'date_added')->textInput() ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
