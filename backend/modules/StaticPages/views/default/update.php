<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\site\StaticPages */

$this->title = 'Update Static Pages: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Static Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="static-pages-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
