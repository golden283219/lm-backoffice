<div class="timeline-item">
    <span class="time">
        <i class="fa fa-clock-o"></i>
      <?php echo Yii::$app->formatter->asRelativeTime($model->created_at) ?>
    </span>

  <h3 class="timeline-header">
    Draft For TV Show: <?= $model->title; ?> / Created By <?= $model->created_by; ?>
  </h3>

  <div class="timeline-body">
    <h3 style="margin: 0;">Changes List:</h3>
    <ol>
    <?php foreach ($model->draftItems as $draftItem): ?>

      <li>
        <p style="font-weight: bold; font-size: 14px; margin: 0;"> <?= $draftItem->controller; ?> → <?= $draftItem->action;?> </p>
        <dl>
          <?php foreach (json_decode($draftItem->data) as $key => $dataItem): ?>
            <dt><?= $key; ?></dt>
            <dd><?= $dataItem; ?></dd>
          <?php endforeach; ?>
        </dl>
      </li>
    <?php endforeach; ?>
    </ol>
  </div>

  <div class="timeline-footer">
    <?php

    echo \yii\helpers\Html::a(
      'View',
      ['/moderation/draft/view', 'id' => $model->id],
      [
        'class' => 'btn btn-primary btn-sm',
      ]
    );

    echo \yii\helpers\Html::a(
      'Apply',
      ['/moderation/draft/execute', 'id' => $model->id],
      [
        'class' => 'btn btn-success btn-sm'
      ]
    );

    echo \yii\helpers\Html::a(
      'Cancel',
      ['/moderation/draft/cancel', 'id' => $model->id],
      [
        'class' => 'btn btn-danger btn-sm'
      ]
    );
    ?>
  </div>
</div>