<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesSearcj */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="movies-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id_movie') ?>

    <?php echo $form->field($model, 'slug') ?>

    <?php echo $form->field($model, 'is_active') ?>

    <?php echo $form->field($model, 'shard_url') ?>

    <?php echo $form->field($model, 'year') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'duration') ?>

    <?php // echo $form->field($model, 'imdb_rating') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'backdrop') ?>

    <?php // echo $form->field($model, 'homepage') ?>

    <?php // echo $form->field($model, 'budget') ?>

    <?php // echo $form->field($model, 'tagline') ?>

    <?php // echo $form->field($model, 'poster') ?>

    <?php // echo $form->field($model, 'views') ?>

    <?php // echo $form->field($model, 'date_added') ?>

    <?php // echo $form->field($model, 'tmdb_prefix') ?>

    <?php // echo $form->field($model, 'has_metadata') ?>

    <?php // echo $form->field($model, 'has_subtitles') ?>

    <?php // echo $form->field($model, 'rel_title') ?>

    <?php // echo $form->field($model, 'rel_os_hash') ?>

    <?php // echo $form->field($model, 'rel_size_bytes') ?>

    <?php // echo $form->field($model, 'youtube') ?>

    <?php // echo $form->field($model, 'release_date') ?>

    <?php // echo $form->field($model, 'has_hash') ?>

    <?php // echo $form->field($model, 'priority') ?>

    <?php // echo $form->field($model, 'storage_slug') ?>

    <?php // echo $form->field($model, 'cast') ?>

    <?php // echo $form->field($model, 'genres') ?>

    <?php // echo $form->field($model, 'flag_quality') ?>

    <?php // echo $form->field($model, 'imdb_id') ?>

    <?php // echo $form->field($model, 'transfer_status') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
