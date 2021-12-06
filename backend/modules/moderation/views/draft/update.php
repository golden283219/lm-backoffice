<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\ModerationDraft */

$this->title = 'Update Moderation Draft: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Moderation Drafts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="moderation-draft-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
