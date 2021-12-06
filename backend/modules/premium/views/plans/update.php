<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PremPlans */

$this->title = 'Update Prem Plans: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Prem Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prem-plans-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
