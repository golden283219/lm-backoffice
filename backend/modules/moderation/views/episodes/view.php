<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ShowsEpisodes */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Shows Episodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-episodes-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'id_shows',
            'is_active',
            'episode',
            'season',
            'title',
            'description:ntext',
            'still_path',
            'shard',
            'storage',
            'subtitles_state',
            'air_date',
            'has_metadata',
            'flag_quality',
            'rel_title',
            'is_locked',
            'quality_approved',
            'finalized_subs',
            'have_all_subs',
            'missing_languages',
            'subs_count',
            'locked_by',
            'locked_at',
        ],
    ]) ?>

</div>
