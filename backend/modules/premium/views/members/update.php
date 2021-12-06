<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PremUsers */

$this->title = 'Update Prem Users: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Prem Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="prem-users-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
