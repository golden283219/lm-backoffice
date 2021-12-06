<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\site\MoviesFeatured */

$this->title = 'Update Movies Featured: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Movies Featureds', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="movies-featured-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
