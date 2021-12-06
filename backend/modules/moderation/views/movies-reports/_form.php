<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesReports */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="movies-reports-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'id_movie')->textInput() ?>

    <?php echo $form->field($model, 'sound_probm')->textInput() ?>

    <?php echo $form->field($model, 'connection_probm')->textInput() ?>

    <?php echo $form->field($model, 'label_probm')->textInput() ?>

    <?php echo $form->field($model, 'video_probm')->textInput() ?>

    <?php echo $form->field($model, 'subs_probm')->textInput() ?>

    <?php echo $form->field($model, 'user_email')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'year')->textInput() ?>

    <?php echo $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'id_user')->textInput() ?>

    <?php echo $form->field($model, 'notify_user')->textInput() ?>

    <?php echo $form->field($model, 'unseen')->textInput() ?>

    <?php echo $form->field($model, 'created_at')->textInput() ?>

    <?php echo $form->field($model, 'is_closed')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
