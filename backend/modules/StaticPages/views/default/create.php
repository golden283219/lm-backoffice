<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\site\StaticPages */

$this->title = 'Create Static Pages';
$this->params['breadcrumbs'][] = ['label' => 'Static Pages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="static-pages-create">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
