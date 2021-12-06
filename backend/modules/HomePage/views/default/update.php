<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\site\HomePage */

$this->title = 'Update Home Page: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Home Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="home-page-update">

    <?php echo $this->render('_form', [
        'model' => $model,
		'collections' => $collections
    ]) ?>

</div>

