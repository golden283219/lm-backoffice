<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\GlobalMessages */

$this->title = 'Create Global Messages';
$this->params['breadcrumbs'][] = ['label' => 'Global Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="global-messages-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
