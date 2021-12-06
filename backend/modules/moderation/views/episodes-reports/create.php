<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\site\ShowsEpisodesReportsCache */

$this->title = 'Create Shows Episodes Reports Cache';
$this->params['breadcrumbs'][] = ['label' => 'Shows Episodes Reports Caches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-episodes-reports-cache-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
