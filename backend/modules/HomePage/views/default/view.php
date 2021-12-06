<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Collection;

/* @var $this yii\web\View */
/* @var $model common\models\site\HomePage */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Home Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="home-page-view">

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
            'icon',
            [
                'attribute' => 'view_type',
                'format' => 'raw',
                'value' => function ($data) {
					$types = [
						'list' => 'Collection',
						'carousel' => 'Featured',
					];

                    return $types[$data->view_type];
                },
            ],
            [
                'attribute' => 'code',
                'format' => 'raw',
                'value' => function ($data) {
                    $code = "<span class='badge badge-info'>" . $data->code . "</span>";
                    return $code;
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'value' => function ($data) {
                    $code = "<span class='badge badge-success'>Active</span>";

					if(!$data->is_active){
						$code = "<span class='badge badge-danger'>Inactive<span>";
					}
                    return $code;
                },
            ],
            [
                'attribute' => 'collection_id',
                'format' => 'raw',
                'value' => function ($data) {
					if($data->collection_id){
						// Get collections
						$collection = Collection::find()
						->select(['title'])
						->where([
							'collection_id' => $data->collection_id
						])
						->one();

					  return Html::a( $collection->title, ['/system/collection/view', 'id' => $data->collection_id], ['class' => '']);
					}

					return $data->collection_id;
                },
            ],
            'section_background',
            'position',
            [
                'attribute' => 'for_premium_user',
                'format' => 'raw',
                'value' => function ($data) {
                    $code = "<span class='badge badge-info'>No</span>";
					if($data->for_premium_user){
						$code = "<span class='badge badge-warning'>Yes</span>";
					}
                    return $code;
                },
            ],
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>

