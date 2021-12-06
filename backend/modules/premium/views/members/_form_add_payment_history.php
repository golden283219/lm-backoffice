<?php

use backend\models\PremPlans;
use backend\models\PremPaymentsHistory;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;


$model->guid = GUIDv4(true);
?>

<div class="prem-users-form">

    <?php $form = ActiveForm::begin([
        'action' => '/premium/members/add-payment-history',
        'id' => 'add-payment-history-form'
    ]); ?>

    <?php echo $form->errorSummary($model); ?>

    <div style="display: none !important;">
        <?php echo $form->field($model, 'id_prem_user'); ?>
        <?php echo $form->field($model, 'created_at'); ?>
        <?php echo $form->field($model, 'guid'); ?>
    </div>

    <div class="form-group field-premusers-payment_status">
        <label class="control-label" for="premusers-status">Payment Status</label>
        <?php echo Html::activeDropDownList($model, 'payment_status', array_map(function ($item) {
            return strip_tags($item);
        }, PremPaymentsHistory::$history_status_vocabulary), ['class' => 'form-control']) ?>
    </div>

    <div class="form-group field-premusers-payment_method">
        <label class="control-label" for="premusers-payment_method">Payment Method</label>
        <?php echo Html::activeDropDownList($model, 'payment_method', PremPaymentsHistory::$payment_method_vocabulary, ['class' => 'form-control']) ?>
    </div>

    <div class="form-group field-premusers-status">
        <label class="control-label" for="premusers-status">Prem Plan</label>
        <?php echo Html::activeDropDownList($model, 'id_prem_plan', PremPlans::allPlans(), ['class' => 'form-control']) ?>
    </div>

    <?= $form->field($model, 'paid_at')->widget(\yii\jui\DatePicker::classname(), [
        'dateFormat' => 'yyyy-MM-dd',
        'options' => [
            'class' => 'form-control',
        ]
    ]) ?>

    <div class="form-group">
        <?php echo Html::submitButton('Add', ['class' => 'btn btn-success', 'id' => 'submit-payment-item']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
