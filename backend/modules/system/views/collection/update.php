<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Collection */

$this->title = 'Update Collection: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->collection_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="collection-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
