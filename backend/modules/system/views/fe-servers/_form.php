<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\modules\system\models\FeServers;

/* @var $this yii\web\View */
/* @var $model common\models\FeServers */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="fe-servers-form">

    <?php $form = ActiveForm::begin([
        'enableClientValidation' => false,
        'enableAjaxValidation' => isset($enableAjaxValidation) && $enableAjaxValidation ? true : false,
    ]); ?>

    <div class="row">
        <div class="col-sm-3">
            <?php echo $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-5">
            <?php echo $form->field($model, 'server_name')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'max_bw')->textInput() ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'is_enabled')->dropDownList([
                '1' => 'Enabled',
                '0'=>'Disabled'
            ]); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-10">
            <?php echo $form->field($model, 'status_check_url')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-2">
            <?php echo $form->field($model, 'is_hidden')->dropDownList([
                '1' => 'True',
                '0' => 'False'
            ]); ?>
        </div>
    </div>

    <div class="row">
        <?php foreach (FeServers::domains as $domain): ?>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="feservers-<?= $domain; ?>"><b style="text-transform: uppercase; color: #4287af;"><?= $domain; ?></b> - Streaming Server</label>
                    <input type="text" id="feservers-<?= $domain; ?>" value="<?= isset($model->domain_mapped[$domain]) ? $model->domain_mapped[$domain] :  ''; ?>" class="form-control" name="FeServers[<?= $domain; ?>]" maxlength="80" aria-required="true">
                    <p class="help-block help-block-error"></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
    </div>
    <div class="clearfix"></div>
    <?php ActiveForm::end(); ?>

</div>
