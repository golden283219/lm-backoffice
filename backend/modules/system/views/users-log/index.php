<?php

use yii\helpers\Html;
use yii\grid\GridView;
use trntv\yii\datetime\DateTimeWidget;
use yii\web\JsExpression;   
use common\models\UsersActionLog;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UsersActionLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users Action Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-action-log-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'id_user',
            'action',
            [
                'attribute' => 'category',
                'value' => function ($model) {
                    return $model->category;
                },
                'filter' => UsersActionLog::GetLogCategories(),
            ],
            [
                'attribute' => 'log_time',
                'format' => 'datetime',
                'value' => function ($model) {   
                    return (int)$model->log_time;
                },
                'filter' => DateTimeWidget::widget([
                    'model' => $searchModel,
                    'attribute' => 'log_time',
                    'phpDatetimeFormat' => 'dd.MM.yyyy',
                    'momentDatetimeFormat' => 'DD.MM.YYYY',
                    'clientEvents' => [
                        'dp.change' => new JsExpression('(e) => $(e.target).find("input").trigger("change.yiiGridView")'),
                    ],
                ]),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>

</div>
