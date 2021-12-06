<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesDownloadQueueSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="movies-download-queue-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'year') ?>

    <?php echo $form->field($model, 'imdb_id') ?>

    <?php echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'is_downloaded') ?>

    <?php // echo $form->field($model, 'source') ?>

    <?php // echo $form->field($model, 'bad_guids') ?>

    <?php // echo $form->field($model, 'bad_titles') ?>

    <?php // echo $form->field($model, 'priority') ?>

    <?php // echo $form->field($model, 'flag_quality') ?>

    <?php // echo $form->field($model, 'worker_ip') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'original_language') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
