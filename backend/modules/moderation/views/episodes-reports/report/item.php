<div class="timeline-item <?= (int)$model->unseen === 0 ? 'assigned' : ''; ?>">
  <span class="time">
    <i class="fa fa-clock-o"></i>
    <?php echo Yii::$app->formatter->asRelativeTime($model->created_at) ?>
  </span>

    <h3 class="timeline-header">
        #<?php echo $model->id; ?>. <?php echo $model->title . ' (' . $model->year . ')'; ?>,
        Season <?= $model->season; ?>
        Episode <?= $model->episode; ?> <?= (int)$model->unseen === 0 ? ' - <b>ASSIGNED TO ME</b>' : ''; ?>
    </h3>

    <div class="timeline-body">

        <ul>
            <?php if ((bool)$model->label_probm): ?>
                <li>
                    <b>Labelling Problem</b>, <br/>
                    Wrong title or summary, or episode out of order
                </li>
            <?php endif; ?>

            <?php if ((bool)$model->video_probm): ?>
                <li>
                    <b>Video Problem</b>, <br/>
                    Blurry, cuts out or looks strange in some way
                </li>
            <?php endif; ?>

            <?php if ((bool)$model->sound_probm): ?>
                <li>
                    <b>Sound Problem</b>, <br/>
                    Hard to hear, not matched with video or missing in some parts
                </li>
            <?php endif; ?>

            <?php if ((bool)$model->subs_probm): ?>
                <li>
                    <b>Subtitles Problem</b>, <br/>
                    Hard to read or not matched with speech
                </li>
            <?php endif; ?>

            <?php if ((bool)$model->connection_probm): ?>
                <li>
                    <b>Buffering or connection problem</b>, <br/>
                    Frequent rebuffering, playback won't start or other problem
                </li>
            <?php endif; ?>

        </ul>

        <p><?= $model->message; ?></p>

        <hr>

        <ul style="padding: 0;" class="inline-list">

            <?php if ($model->user_email && $model->user_email !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><i
                            class="fa fa-envelope-o">&nbsp;</i> <?= urldecode($model->user_email); ?></li>
            <?php endif; ?>

            <?php if ($model->browser && $model->browser !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><i class="fa fa-internet-explorer"
                                                                  aria-hidden="true"></i>&nbsp; <?= urldecode($model->browser); ?>
                </li>
            <?php endif; ?>

            <?php if ($model->os && $model->os !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><i class="fa fa-windows"
                                                                  aria-hidden="true"></i>&nbsp; <?= urldecode($model->os); ?>
                </li>
            <?php endif; ?>

            <?php if ($model->country && $model->country !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><img class="flag flag-<?= strtolower($model->iso); ?>"
                                                                    src="/flags/blan.gif"
                                                                    alt="<?= $model->country; ?>"> <?= urldecode($model->country); ?>
                    (<?= long2ip($model->ip_addr); ?>)
                </li>
            <?php endif; ?>

            <?php if ($model->fe_server && $model->fe_server !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><i class="fa fa-server"></i> <?= $model->fe_server; ?>
                </li>
            <?php endif; ?>

            <?php if ($model->src_quality && $model->src_quality !== ''): ?>
                <li class="inline" style="margin-right: 20px;"><i class="fa fa-video-camera"
                                                                  aria-hidden="true"></i> <?= $model->src_quality; ?>
                </li>
            <?php endif; ?>

        </ul>

    </div>

    <div class="timeline-footer">
        <?php
        echo \yii\helpers\Html::a(
            'Close Ticket #' . $model->id . '!',
            ['/moderation/episodes-reports/close-ticket', 'id' => $model->id],
            [
                'class' => 'btn btn-primary btn-sm',
                'data-pjax' => 1,
                'data-confirm' => 'Are you sure you want to close this ticket?'
            ]
        );
        ?>
    </div>
</div>
