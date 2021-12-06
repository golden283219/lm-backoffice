<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CollectionData;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\system\models\search\CollectionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Collections';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="collection-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php echo Html::a('Create Collection', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

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
             'type',
             'script_position',
             'last_data_update:datetime',
			   [
					'attribute' => 'total_changes',
					'value' => function($model){
						$total_changes = $model->last_added_count + $model->last_deleted_count;
						return $total_changes;
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
            // 'paginated',
            // 'description:ntext',
            // 'is_active',
            // 'position',
            // 'last_data_update',
            // 'created_at',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
