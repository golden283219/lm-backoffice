<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\Movies */
/* @var $form yii\bootstrap\ActiveForm */

?>

<div class="movies-form">

    <?php $form = ActiveForm::begin(); ?>


  <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'id_movie')->textInput() ?>

    <?php echo $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'shard_url')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'year')->textInput() ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'duration')->textInput() ?>

    <?php echo $form->field($model, 'imdb_rating')->textInput() ?>

    <?php echo $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'backdrop')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'homepage')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'budget')->textInput() ?>

    <?php echo $form->field($model, 'tagline')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'poster')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'views')->textInput() ?>

    <?php echo $form->field($model, 'date_added')->textInput() ?>

    <?php echo $form->field($model, 'tmdb_prefix')->textInput() ?>

    <?php echo $form->field($model, 'has_metadata')->textInput() ?>

    <?php echo $form->field($model, 'has_subtitles')->textInput() ?>

    <?php echo $form->field($model, 'rel_title')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'rel_os_hash')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'rel_size_bytes')->textInput() ?>

    <?php echo $form->field($model, 'youtube')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'release_date')->textInput() ?>

    <?php echo $form->field($model, 'has_hash')->textInput() ?>

    <?php echo $form->field($model, 'priority')->textInput() ?>

    <?php echo $form->field($model, 'storage_slug')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'cast')->textInput() ?>

    <?php echo $form->field($model, 'genres')->textInput() ?>

    <?php echo $form->field($model, 'flag_quality')->textInput() ?>

    <?php echo $form->field($model, 'imdb_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'transfer_status')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
