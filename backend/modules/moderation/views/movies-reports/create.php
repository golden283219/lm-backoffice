<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesReports */

$this->title = 'Create Movies Reports';
$this->params['breadcrumbs'][] = ['label' => 'Movies Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movies-reports-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
