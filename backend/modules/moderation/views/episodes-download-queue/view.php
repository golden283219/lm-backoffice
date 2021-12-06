<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\queue\ShowsMeta */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shows Metas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-meta-view">

    <p>
        <?php echo Html::a('<i class="fa fa-arrow-circle-left" aria-hidden="true"></i> Go Back', \Yii::$app->request->getReferrer(), ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Update', ['update', 'id' => $model->id_meta], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_meta',
            'id_tvshow',
            'season',
            'episode',
            'title',
            'air_date',
            'worker_ip',
            'bad_guids',
            'bad_titles',
            'state',
            'priority',
            'torrent_blob',
            'type',
            'rel_title',
        ],
    ]) ?>

</div>
