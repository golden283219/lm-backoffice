<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ShowsEpisodes */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="shows-episodes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'id_shows')->textInput() ?>

    <?php echo $form->field($model, 'is_active')->textInput() ?>

    <?php echo $form->field($model, 'episode')->textInput() ?>

    <?php echo $form->field($model, 'season')->textInput() ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'still_path')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'shard')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'storage')->textInput() ?>

    <?php echo $form->field($model, 'subtitles_state')->textInput() ?>

    <?php echo $form->field($model, 'air_date')->textInput() ?>

    <?php echo $form->field($model, 'has_metadata')->textInput() ?>

    <?php echo $form->field($model, 'flag_quality')->textInput() ?>

    <?php echo $form->field($model, 'rel_title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'is_locked')->textInput() ?>

    <?php echo $form->field($model, 'quality_approved')->textInput() ?>

    <?php echo $form->field($model, 'finalized_subs')->textInput() ?>

    <?php echo $form->field($model, 'have_all_subs')->textInput() ?>

    <?php echo $form->field($model, 'missing_languages')->textInput() ?>

    <?php echo $form->field($model, 'subs_count')->textInput() ?>

    <?php echo $form->field($model, 'locked_by')->textInput() ?>

    <?php echo $form->field($model, 'locked_at')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
