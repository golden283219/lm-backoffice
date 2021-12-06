<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Collection */

$this->title = 'Create Collection';
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="collection-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
