<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\site\MoviesFeatured */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="movies-featured-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'id_movie')->dropdownList(
        [],
        [
            'prompt' => 'Search Movie',
            'class' => 'form-control select-box'
        ]
    ); ?>

    <?php echo $form->field($model, 'position')->textInput(['type' => 'number', 'min' => 0, 'value' => $model->isNewRecord ? 0 : $model->position])?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
