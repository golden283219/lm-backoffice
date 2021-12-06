<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\EpisodesModerationHistory */

$this->title = 'Update Episodes Moderation History: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Episodes Moderation Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="episodes-moderation-history-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
