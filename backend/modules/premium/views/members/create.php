<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PremUsers */

$this->title = 'Create Prem Users';
$this->params['breadcrumbs'][] = ['label' => 'Prem Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prem-users-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
