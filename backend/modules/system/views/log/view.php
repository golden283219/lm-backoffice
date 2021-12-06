<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this  yii\web\View
 * @var $model backend\modules\system\models\SystemLog
 */

$levels = [
    \yii\log\Logger::LEVEL_ERROR => 'Error',
    \yii\log\Logger::LEVEL_WARNING => 'Warning',
    \yii\log\Logger::LEVEL_INFO => 'Info',
    \yii\log\Logger::LEVEL_TRACE => 'Trace',
    \yii\log\Logger::LEVEL_PROFILE_BEGIN => 'Profile begin',
    \yii\log\Logger::LEVEL_PROFILE_END => 'Profile end',
];

$this->title = Yii::t('backend', '{type} #{id}', ['id' => $model->id, 'type' => $levels[$model->level]]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('backend', 'System Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php echo Html::a(Yii::t('backend', 'Delete'), ['delete', 'id' => $model->id], ['class' => 'btn btn-danger', 'data' => ['method' => 'post']]) ?>
</p>

<?php echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'level',
        'category',
        [
            'attribute' => 'log_time',
            'format' => 'datetime',
            'value' => (int)$model->log_time,
        ],
        'prefix:ntext',
        [
            'attribute' => 'message',
            'format' => 'raw',
            'value' => Html::tag('pre', $model->message, ['style' => 'white-space: pre-wrap']),
        ],
    ],
]) ?>
