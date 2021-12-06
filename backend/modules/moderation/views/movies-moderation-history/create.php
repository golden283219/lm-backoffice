<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesModerationHistory */

$this->title = 'Create Movies Moderation History';
$this->params['breadcrumbs'][] = ['label' => 'Movies Moderation Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movies-moderation-history-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
