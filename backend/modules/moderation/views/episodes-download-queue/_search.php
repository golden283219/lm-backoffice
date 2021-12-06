<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\queue\ShowsMetaSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="shows-meta-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id_meta') ?>

    <?php echo $form->field($model, 'id_tvshow') ?>

    <?php echo $form->field($model, 'season') ?>

    <?php echo $form->field($model, 'episode') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'air_date') ?>

    <?php // echo $form->field($model, 'worker_ip') ?>

    <?php // echo $form->field($model, 'bad_guids') ?>

    <?php // echo $form->field($model, 'bad_titles') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'priority') ?>

    <?php // echo $form->field($model, 'torrent_blob') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'rel_title') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
