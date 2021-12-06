<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\GlobalMessages */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="global-messages-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation' => isset($enableAjaxValidation) && $enableAjaxValidation ? true : false,
    ]); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php 
        $options = [];
        $options['height'] = 100;

        $options['toolbarGroups'] = [
            ['name' => 'undo'],
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'colors'],
            ['name' => 'links', 'groups' => ['links']],
            ['name' => 'others','groups' => ['others', 'about']],
        ];
        $options['removeButtons'] = 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe';
        $options['removePlugins'] = 'elementspath';
        $options['resize_enabled'] = false;

        echo $form->field($model, 'content')->widget(CKEditor::className(),[
        'editorOptions' => $options,
    ]);
    ?>

    <?php echo $form->field($model, 'type')->textInput() ?>

    <?php echo $form->field($model, 'priority')->textInput() ?>

    <?php echo $form->field($model, 'is_active')->dropDownList(['1' => 'Active', '0' => 'Inactive'])?>

    <?= $form->field($model, 'date_start')->widget(\yii\jui\DatePicker::classname(), [
     //'language' => 'ru',
     'dateFormat' => 'yyyy-MM-dd',
     'options' => [
         'class' => 'form-control',
     ]
 ]) ?>

    <?= $form->field($model, 'date_end')->widget(\yii\jui\DatePicker::classname(), [
     //'language' => 'ru',
     'dateFormat' => 'yyyy-MM-dd',
     'options' => [
         'class' => 'form-control',
     ]
 ]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
