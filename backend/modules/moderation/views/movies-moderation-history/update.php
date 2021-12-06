<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesModerationHistory */

$this->title = 'Update Movies Moderation History: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Movies Moderation Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="movies-moderation-history-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
