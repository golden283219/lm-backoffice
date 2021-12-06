<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\site\ShowsEpisodesReportsCache */

$this->title = 'Update Shows Episodes Reports Cache: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Shows Episodes Reports Caches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shows-episodes-reports-cache-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
