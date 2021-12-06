<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ShowsEpisodes */

$this->title = 'Create Shows Episodes';
$this->params['breadcrumbs'][] = ['label' => 'Shows Episodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-episodes-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
