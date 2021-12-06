<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesReportsSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="movies-reports-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'id_movie') ?>

    <?php echo $form->field($model, 'sound_probm') ?>

    <?php echo $form->field($model, 'connection_probm') ?>

    <?php echo $form->field($model, 'label_probm') ?>

    <?php // echo $form->field($model, 'video_probm') ?>

    <?php // echo $form->field($model, 'subs_probm') ?>

    <?php // echo $form->field($model, 'user_email') ?>

    <?php // echo $form->field($model, 'slug') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'year') ?>

    <?php // echo $form->field($model, 'message') ?>

    <?php // echo $form->field($model, 'id_user') ?>

    <?php // echo $form->field($model, 'notify_user') ?>

    <?php // echo $form->field($model, 'unseen') ?>

    <?php // echo $form->field($model, 'is_closed') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
