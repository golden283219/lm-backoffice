<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PremPlans */

$this->title = 'Create Prem Plans';
$this->params['breadcrumbs'][] = ['label' => 'Prem Plans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prem-plans-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
