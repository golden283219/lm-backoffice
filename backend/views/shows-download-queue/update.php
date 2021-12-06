<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\queue\Shows */

$this->title = 'Update Shows: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id_tvshow]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shows-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
