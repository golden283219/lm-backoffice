<?php

use backend\modules\moderation\models\ModerationDraft;

$this->title = Yii::t('backend', 'Moderation Drafts');
$icons = [
  'user'=>'<i class="fa fa-user bg-blue"></i>'
];
?>

<div class="row">
  <div class="col-md-12">
    <?php if ($dataProvider->count > 0): ?>
      <ul class="timeline">
        <?php foreach($dataProvider->getModels() as $model): ?>
          <?php if(!isset($date) || $date != Yii::$app->formatter->asDate($model->created_at)): ?>
            <!-- timeline time label -->
            <li class="time-label">
              <span class="bg-blue">
                <?php echo Yii::$app->formatter->asDate($model->created_at) ?>
              </span>
            </li>
            <?php $date = Yii::$app->formatter->asDate($model->created_at) ?>
          <?php endif; ?>
          <li>
            <?php
            // try {
              if ((int)$model->category === ModerationDraft::CATEGORY_MOVIES) {
                echo $this->render('templates/movie_item', ['model' => $model]);
              } else if ((int)$model->category === ModerationDraft::CATEGORY_TVSHOWS) {
                echo $this->render('templates/tvshow_item', ['model' => $model]);
              } else if ((int)$model->category === ModerationDraft::CATEGORY_TVEPISODES) {
                echo $this->render('templates/episode_item', ['model' => $model]);
              } else {
                echo $this->render('_item', ['model' => $model]);
              }
              
            // } catch (\yii\base\InvalidArgumentException $e) {
            //   echo $this->render('_item', ['model' => $model]);
            // }
            ?>
          </li>
        <?php endforeach; ?>
        <li>
          <i class="fa fa-clock-o">
          </i>
        </li>
      </ul>
    <?php else: ?>
      <?php echo Yii::t('backend', 'No movies reports found.') ?>
    <?php endif; ?>
  </div>
  <div class="col-md-12 text-center">
    <?php echo \yii\widgets\LinkPager::widget([
      'pagination'=>$dataProvider->pagination,
      'options' => ['class' => 'pagination']
    ]) ?>
  </div>
</div>