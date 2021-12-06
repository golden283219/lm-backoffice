<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\system\models\GlobalMessages */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Global Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="global-messages-view">

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
            'title',
            [
                'attribute' => 'is_active',
                'format' => 'html',
                'value' => function($data){
                    $html_text = '<span class="badge badge-secondary">Inactive</span>';
                    if($data->is_active) $html_text = '<span class="badge badge-success">Active</span>';

                    return $html_text;
                }
            ],
            [
                'attribute' => 'content',
                'format' => 'html'
            ],
            'type',
            'priority',
            'date_start',
            'date_end',
        ],
    ]) ?>

</div>
