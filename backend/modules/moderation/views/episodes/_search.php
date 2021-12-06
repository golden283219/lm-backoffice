<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ShowsEpisodesSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="shows-episodes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'id_shows') ?>

    <?php echo $form->field($model, 'is_active') ?>

    <?php echo $form->field($model, 'episode') ?>

    <?php echo $form->field($model, 'season') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'still_path') ?>

    <?php // echo $form->field($model, 'shard') ?>

    <?php // echo $form->field($model, 'storage') ?>

    <?php // echo $form->field($model, 'subtitles_state') ?>

    <?php // echo $form->field($model, 'air_date') ?>

    <?php // echo $form->field($model, 'has_metadata') ?>

    <?php // echo $form->field($model, 'flag_quality') ?>

    <?php // echo $form->field($model, 'rel_title') ?>

    <?php // echo $form->field($model, 'is_locked') ?>

    <?php // echo $form->field($model, 'quality_approved') ?>

    <?php // echo $form->field($model, 'finalized_subs') ?>

    <?php // echo $form->field($model, 'have_all_subs') ?>

    <?php // echo $form->field($model, 'missing_languages') ?>

    <?php // echo $form->field($model, 'subs_count') ?>

    <?php // echo $form->field($model, 'locked_by') ?>

    <?php // echo $form->field($model, 'locked_at') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
