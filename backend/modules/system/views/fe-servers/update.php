<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\FeServers */

$this->title = 'Update Server #' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Streaming Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="fe-servers-update">
	<div class="box">
		<div class="box-body">
			<?php echo $this->render('_form', [
				'model' => $model,
			]) ?>
		</div>
	</div>
</div>
