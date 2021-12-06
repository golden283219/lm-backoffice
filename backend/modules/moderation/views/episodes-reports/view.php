<?php

/**
 * @var $dataProvider
 */

$icons = [
'user'=>'<i class="fa fa-user bg-blue"></i>'
];
?>
<?php if($dataProvider->count > 0): ?>
    <?php $model = $dataProvider->models['0']; ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-body">
                    <div class="rel_title pull-left reconvert-hidden">
                        <?= '#'.$model->id.' '.$model->title.' (' . $model->year . ') Season'. $model->season.' Episode ' . $model->episode; ?>
                    </div>
                    <div class="controls pull-right">
                        <a href="/moderation/episodes/update?id=<?= $model->id_episode; ?>" type="button" class="btn btn-primary">
                            View Episode
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
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
                        try {
                            echo $this->render('report/item', ['model' => $model]);
                        } catch (\yii\base\InvalidArgumentException $e) {
                            echo $this->render('_item', ['model' => $model]);
                        }
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
