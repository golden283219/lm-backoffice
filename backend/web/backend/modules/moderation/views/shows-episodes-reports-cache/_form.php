<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\site\ShowsEpisodesReportsCache */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="shows-episodes-reports-cache-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'count')->textInput() ?>

    <?php echo $form->field($model, 'id_episode')->textInput() ?>

    <?php echo $form->field($model, 'last_reported_at')->textInput() ?>

    <?php echo $form->field($model, 'assigned_user_id')->textInput() ?>

    <?php echo $form->field($model, 'is_closed')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
