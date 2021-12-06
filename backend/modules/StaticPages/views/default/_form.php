<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model common\models\site\StaticPages */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="static-pages-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

  <div class="row">
    <div class="col-sm-6"><?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?></div>
    <div class="col-sm-6"><?php echo $form->field($model, 'slug')->textInput(['maxlength' => true]) ?></div>
  </div>

  <div class="row">
    <div class="col-sm-12">
        <?php echo $form->field($model, 'contents')->widget(CKEditor::className(), [
            'editorOptions' => [
                'preset' => 'full',
                'inline' => false,
            ],
        ]); ?>
    </div>
  </div>

  <div class="form-group">
      <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
  </div>

    <?php ActiveForm::end(); ?>

</div>
