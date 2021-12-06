<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\queue\ShowsSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="shows-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id_tvshow') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'first_air_date') ?>

    <?php echo $form->field($model, 'imdb_id') ?>

    <?php echo $form->field($model, 'tmdb_id') ?>

    <?php // echo $form->field($model, 'tvmaze_id') ?>

    <?php // echo $form->field($model, 'total_episodes') ?>

    <?php // echo $form->field($model, 'total_seasons') ?>

    <?php // echo $form->field($model, 'episode_duration') ?>

    <?php // echo $form->field($model, 'in_production') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'date_added') ?>

    <?php // echo $form->field($model, 'data') ?>

    <?php // echo $form->field($model, 'original_language') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
