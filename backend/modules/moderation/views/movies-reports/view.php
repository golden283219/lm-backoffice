<?php

/**
 * @var $reports
 * @var $movie Movies
 */

$this->title = $movie->title . '(' . $movie->year . ') - Reports';
$this->params['breadcrumbs'][] = ['label' => 'Movies Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\VideoPlaybackAsset::register($this);
\backend\assets\MoviesReportsView::register($this);

use backend\models\site\Movies; ?>
<div class="movies-reports-view">
    <div class="row">
        <div class="col-sm-12">
            <div class="box">
                <div class="box-body">
                    <div class="controls pull-left">
                        <a href="/moderation/movies-reports" class="btn btn-link"><i class="fa fa-chevron-left" aria-hidden="true"></i> All Reports</a>
                    </div>
                    <div class="controls pull-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">Actions <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="/moderation/movies/update?id=<?= $movie->id_movie; ?>">
                                        Moderate Movie #<?= $movie->id_movie; ?></a>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="/moderation/movies-reports/close-all?id_movie=<?= $movie->id_movie; ?>">Close
                                        All Tickets!</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?php if ($reports > 0): ?>
                <ul class="timeline">
                    <?php foreach ($reports as $report): ?>
                        <?php if (!isset($date) || $date != \Yii::$app->formatter->asDate($report->created_at)): ?>
                            <!-- timeline time label -->
                            <li class="time-label">
                                <span class="bg-blue">
                                    <?php echo \Yii::$app->formatter->asDate($report->created_at) ?>
                                </span>
                            </li>
                            <?php $date = \Yii::$app->formatter->asDate($report->created_at) ?>
                        <?php endif; ?>
                            <li>
                                <?php
                                    try {
                                        echo $this->render('report/report-item', ['report' => $report]);
                                    } catch (\yii\base\InvalidArgumentException $e) {
                                        echo $this->render('_item', ['report' => $report]);
                                    }
                                ?>
                            </li>
                    <?php endforeach; ?>
                    <li>
                        <i class="fa fa-clock-o">
                        </i>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
