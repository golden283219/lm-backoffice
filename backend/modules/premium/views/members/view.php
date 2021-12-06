<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\PremUsers */

$this->title = 'User: ' . $model->email;
$this->params['breadcrumbs'][] = ['label' => 'Prem Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prem-users-view">

    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email',
            'status',
            'plain_password',
            [
                'attribute' => 'token_key',
                'label' => 'App Login Token'
            ],
        ],
    ]) ?>

</div>
