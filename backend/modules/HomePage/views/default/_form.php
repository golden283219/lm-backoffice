<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\site\HomePage */
/* @var $form yii\bootstrap\ActiveForm */

// Generate variable to set dropdown list for collections
$collectionsList = [];
foreach($collections as $collection){
	$collectionsList[$collection['id']] = $collection['title'];
}

//Set default color for model
if(!$model->section_background){
	$model->section_background = '#ffffff';
}
?>

<div class="home-page-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

	<?php 
		$options = [
			'maxlength' => true, 
		];

		if($model->code){
			$options['disabled'] = true;	
			$options['readonly'] = true;	
		}

		echo $form->field($model, 'code')
			->textInput($options) 
	?>


	<?php echo $form->field($model, 'collection_id')->dropDownList($collectionsList, ['prompt'=>'-- Select Collection --']); ?>

    <?php echo $form->field($model, 'icon')->textInput(['maxlength' => true]) ?>
    
    <?php echo $form->field($model, 'position')->textInput(['type' => 'number']) ?>

    <?php echo $form->field($model, 'section_background')->textInput(['type' => 'color']) ?>

	<?php echo $form->field($model, 'for_premium_user')->dropDownList([
		'0' => 'No',
		'1' => 'Yes'
	]); ?>

	<?php echo $form->field($model, 'is_active')->dropDownList([
		'0' => 'No',
		'1' => 'Yes'
	]); ?>

	<?php echo $form->field($model, 'view_type')->dropDownList([
		'carousel' => 'Featured',
		'list' => 'Collection'
	]); ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

