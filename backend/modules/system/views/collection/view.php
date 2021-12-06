<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\dynagrid\DynaGrid;
use common\models\CollectionData;

/* @var $this yii\web\View */
/* @var $model common\models\Collection */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="collection-view">

    <p>
        <?php echo Html::a('Update', ['update', 'id' => $model->collection_id], ['class' => 'btn btn-primary']) ?>
        <?php echo Html::a('Delete', ['delete', 'id' => $model->collection_id], [
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
            'collection_id',
            'title',
            'slug',
		   [
				'attribute' => 'url',
				'format' => 'html',
				'value' => function($model){
					$html = '<a href="'  . $model->url . '">Link</a>';
					return $html;
				}
			],
            'script_position',
            'type',
            'description:ntext',
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
				'attribute' => 'Items Count',
				'value' => function($model){
					$count = CollectionData::find()
						->where(['collection_id' => $model->collection_id])
						->count();

					return $count;
				}
			],
            'created_at:datetime',
            'updated_at:datetime',
            'last_data_update:datetime',
        ],
    ]) ?>

	<?php 
		$columns = [
			['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
			[
				'attribute' => 'collection_id',
				'filter' => false
			],
			'started',
			'finished',
			'total_changes',
		];

		$toolbar = [
			'{export}',
			'{toggleData}',
			'{dynagrid}',
		];

		echo DynaGrid::widget([
			'columns' => $columns,
			'storage' => DynaGrid::TYPE_COOKIE,
			'theme' => 'panel-info',
			'allowPageSetting' => false,
			'gridOptions' => [
				'itemLabelSingle' => 'movie',
				'itemLabelPlural' => 'movies',
				'showPageSummary' => false,
				'dataProvider' => $dataHistoryProvider,
				'filterModel' => $searchHistoryModel,
				'panel' => [
					'heading' => '<h3 class="panel-title">Collection History</h3>',
				],
				'toolbar' => $toolbar,
				'hover' => true,
				'responsive' => false,
				'pjax' => true,
			],
			'options' => ['id' => 'grid-yt-converters-1', 'class' => 'expandable-row']
		]);

		#
		$columns = [
			['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
			[
				'label' => 'Title',
				'attribute' => 'title',
				'format' => function ($model) {
					return $this->render('_m-card', [
						'model' => $model
					]);
				},
				'value' => function ($model) {
					return $model;
				},
			],
			'type',
			'date_added',
			[
				'label' => 'Is Enabled',
				'format' => function ($value) {
					$formatted_value = '(not set)';
					switch ($value) {
						case '0':
							$formatted_value = '<span class="badge badge-danger">DISABLED</span>';
							break;

						case '1':
							$formatted_value = '<span class="badge badge-success">ENABLED</span>';
							break;

						default:
							$formatted_value = '(not set)';
							break;
					}
					return $formatted_value;
				},
				'attribute' => 'is_active',
				'filter' => [
					0 => 'DISABLED',
					1 => 'ENABLED'
				],
				'value' => function ($model) {
					return $model['is_active'];
				},
			],
		];

		$toolbar = [
			'{export}',
			'{toggleData}',
			'{dynagrid}',
		];

		echo DynaGrid::widget([
			'columns' => $columns,
			'storage' => DynaGrid::TYPE_COOKIE,
			'theme' => 'panel-info',
			'allowPageSetting' => false,
			'gridOptions' => [
				'itemLabelSingle' => 'movie',
				'itemLabelPlural' => 'movies',
				'showPageSummary' => false,
				'dataProvider' => $dataProvider,
				'panel' => [
					'heading' => '<h3 class="panel-title">Collection Items<h3>',
				],
				'toolbar' => $toolbar,
				'hover' => true,
				'responsive' => false,
				'pjax' => true,
			],
			'options' => ['id' => 'grid-yt-converters-2', 'class' => 'expandable-row']
		]);
	?>


</div>
