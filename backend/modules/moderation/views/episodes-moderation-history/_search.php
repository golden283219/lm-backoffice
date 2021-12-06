<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\EpisodesModerationHistorySearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="episodes-moderation-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'id_meta') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'imdb_id') ?>

    <?php echo $form->field($model, 'tvmaze_id') ?>

    <?php // echo $form->field($model, 'air_date') ?>

    <?php // echo $form->field($model, 'priority') ?>

    <?php // echo $form->field($model, 'original_language') ?>

    <?php // echo $form->field($model, 'id_user') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'guid') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'worker_ip') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
