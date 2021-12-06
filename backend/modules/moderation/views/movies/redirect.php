<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\Movies */

$this->title = 'Movies Moderation Redirect';
$this->params['breadcrumbs'][] = ['label' => 'Movies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="movies-view">

    <p style="text-align: center;">
        Seems Movie #<?= $id;?> being moderated by another moderator or you dont have access to it.
    </p>
    <p style="text-align: center;">
        <?php echo Html::a('GoTo Moderation List', ['index', 'MoviesSearcj[is_locked]' => 0], [
            'class' => 'btn btn-primary',
        ]) ?>
    </p>

</div>
