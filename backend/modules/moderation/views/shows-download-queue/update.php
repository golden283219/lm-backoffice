<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\queue\Shows */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id_tvshow]];
$this->params['breadcrumbs'][] = 'Update';
?>

<p>
    <?php echo Html::a('<i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Go Back', \Yii::$app->request->getReferrer(), ['class' => 'btn btn-primary']) ?>
    <?php echo Html::a('<i class="fa fa-circle-o" aria-hidden="true"></i> View All Episodes', '/moderation/episodes-download-queue?ShowsMetaSearch%5Bid_tvshow%5D=' . $model->id_tvshow, ['class' => 'btn btn-success']) ?>
</p>

<div class="shows-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
