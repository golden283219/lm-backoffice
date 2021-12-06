<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\FeServers */

$this->title = 'Create Fe Servers';
$this->params['breadcrumbs'][] = ['label' => 'Fe Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fe-servers-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
