<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesModerationHistory */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="movies-moderation-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'id_movie')->textInput() ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'imdb_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'year')->textInput() ?>

    <?php echo $form->field($model, 'priority')->textInput() ?>

    <?php echo $form->field($model, 'original_language')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'id_user')->textInput() ?>

    <?php echo $form->field($model, 'status')->textInput() ?>

    <?php echo $form->field($model, 'data')->textInput() ?>

    <?php echo $form->field($model, 'guid')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'type')->textInput() ?>

    <?php echo $form->field($model, 'worker_ip')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'created_at')->textInput() ?>

    <?php echo $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
