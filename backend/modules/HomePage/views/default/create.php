<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\site\HomePage */

$this->title = 'Create Home Page';
$this->params['breadcrumbs'][] = ['label' => 'Home Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="home-page-create">

    <?php echo $this->render('_form', [
        'model' => $model,
		'collections' => $collections
    ]) ?>

</div>

