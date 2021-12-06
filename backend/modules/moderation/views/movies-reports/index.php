<?php

$this->registerCssFile('/flags/flags.min.css');

$this->title = Yii::t('backend', 'Movies Reports');
$icons = [
    'user'=>'<i class="fa fa-user bg-blue"></i>'
];
?>

<div class="row">
    <div class="col-md-12">
        <div class="reports-wrapper">
            <?php foreach($dataProvider->getModels() as $model): ?>
                <?php
                    try {
                        echo $this->render('report/movie-item', ['model' => $model]);
                    } catch (\yii\base\InvalidArgumentException $e) {
                        echo $this->render('_item', ['model' => $model]);
                    }
                ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-12 text-center">
        <?php echo \yii\widgets\LinkPager::widget([
          'pagination'=>$dataProvider->pagination,
          'options' => ['class' => 'pagination']
        ]) ?>
  </div>
</div>
