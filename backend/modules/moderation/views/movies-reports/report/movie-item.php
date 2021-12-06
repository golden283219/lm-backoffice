<div class="report-item <?= isset($model->locked_by) ? 'assigned' : '';?>">

  <div class="report-header">
    <h3>
      <?= isset($model->locked_by) ? 'ASSIGNED TO ME': ''; ?>
    </h3>
    <span class="time">
      <i class="fa fa-clock-o"></i>
      <?php echo Yii::$app->formatter->asRelativeTime($model->latest_reports_timestamp) ?>
    </span>
  </div>

  <div class="report-body">
    <div class="report-body__inner-wrapper">
      <div class="report-body__inner-wrapper--image">
        <div class="report-body__inner-wrapper--image-inner">
          <img src="<?= \Yii::$app->imageStorage::poster('w300', $model->movies->poster);?>" alt="<?= $model->movies->title; ?>">
        </div>
      </div>
      <div class="report-body__inner-wrapper--meta">
        <h2><?= $model->movies->title; ?> <span>(<?= $model->movies->year; ?>)</span></h2>
        <div class="reports-count">Reports Count: <span><?= $model->active_reports_count; ?></span></div>
        <div class="imdb-id"><a target="_blank" href="https://imdb.com/title/tt<?= $model->movies->imdb_id; ?>">IMDb: tt<?= $model->movies->imdb_id; ?></a></div>
        <div class="report-buttons">
            <?php
                echo \yii\helpers\Html::a(
                    'View Reports',
                    ['/moderation/movies-reports/view', 'id' => $model->id_movie],
                    [
                        'class' => 'btn btn-primary btn-sm'
                    ]
                );

                echo \yii\helpers\Html::a(
                    'Close Tickets',
                    ['/moderation/movies-reports/close-all', 'id_movie' => $model->id_movie],
                    [
                        'class' => 'btn btn-danger btn-sm',
                        'data-pjax' => 1,
                        'data-confirm' => 'Are you sure you want to close all tickets ?'
                    ]
                );
            ?>
        </div>
      </div>
    </div>
  </div>
</div>
