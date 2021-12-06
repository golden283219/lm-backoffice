<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\site\ShowsEpisodesReportsCacheSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="shows-episodes-reports-cache-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'count') ?>

    <?php echo $form->field($model, 'id_episode') ?>

    <?php echo $form->field($model, 'last_reported_at') ?>

    <?php echo $form->field($model, 'assigned_user_id') ?>

    <?php // echo $form->field($model, 'is_closed') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
