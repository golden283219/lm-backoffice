<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesDownloadQueue */

$this->title = 'Update: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Movies Download Queue', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="movies-download-queue-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
