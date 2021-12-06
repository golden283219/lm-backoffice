<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\ModerationDraft */

$this->title = 'Create Moderation Draft';
$this->params['breadcrumbs'][] = ['label' => 'Moderation Drafts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="moderation-draft-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
