<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\queue\Shows */

$this->title = 'Create Shows';
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
