<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\EpisodesModerationHistory */

$this->title = 'Create Episodes Moderation History';
$this->params['breadcrumbs'][] = ['label' => 'Episodes Moderation Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="episodes-moderation-history-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
