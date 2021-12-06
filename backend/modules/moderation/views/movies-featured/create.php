<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\site\MoviesFeatured */

$this->title = 'Add Movie to Featured Movies';
$this->params['breadcrumbs'][] = ['label' => 'Movies Featureds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movies-featured-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
