<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\DraftSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="moderation-draft-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'id_media') ?>

    <?php echo $form->field($model, 'title') ?>

    <?php echo $form->field($model, 'category') ?>

    <?php echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'executed_by') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
