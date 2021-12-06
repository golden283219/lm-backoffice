<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\queue\ShowsMeta */

$this->title = 'Create Shows Meta';
$this->params['breadcrumbs'][] = ['label' => 'Shows Metas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-meta-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
