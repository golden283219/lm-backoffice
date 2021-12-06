<?php 
$flags = \backend\assets\FlagsAsset::register($this);
?>
<div class="timeline-item">
  <span class="time">
    <i class="fa fa-clock-o"></i>
      <?php echo Yii::$app->formatter->asRelativeTime($report->created_at) ?>
  </span>

    <h3 class="timeline-header">
        #<?php echo $report->id; ?>
    </h3>

    <div class="timeline-body">
        <ul>
            <?php if ((bool)$report->label_probm): ?>
                <li>
                    <b>Labelling Problem</b>, <br/>
                    Wrong title or summary, or episode out of order
                </li>
            <?php endif; ?>

            <?php if ((bool)$report->video_probm): ?>
                <li>
                    <b>Video Problem</b>, <br/>
                    Blurry, cuts out or looks strange in some way
                </li>
            <?php endif; ?>

            <?php if ((bool)$report->sound_probm): ?>
                <li>
                    <b>Sound Problem</b>, <br/>
                    Hard to hear, not matched with video or missing in some parts
                </li>
            <?php endif; ?>

            <?php if ((bool)$report->subs_probm): ?>
                <li>
                    <b>Subtitles Problem</b>, <br/>
                    Hard to read or not matched with speech
                </li>
            <?php endif; ?>

            <?php if ((bool)$report->connection_probm): ?>
                <li>
                    <b>Buffering or connection problem</b>, <br/>
                    Frequent rebuffering, playback won't start or other problem
                </li>
            <?php endif; ?>

        </ul>

        <p><?= $report->message; ?></p>

        <hr>

        <ul style="padding: 0;" class="inline-list">

            <?php if($report->user_email && $report->user_email !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><i class="fa fa-envelope-o">&nbsp;</i> <?= urldecode($report->user_email); ?></li>
            <?php endif; ?>

            <?php if($report->browser && $report->browser !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><i class="fa fa-internet-explorer" aria-hidden="true"></i>&nbsp; <?= urldecode($report->browser); ?></li>
            <?php endif; ?>

            <?php if($report->os && $report->os !== ''): ?>
                <li class="inline" style="margin-right: 15px;"><i class="fa fa-windows" aria-hidden="true"></i>&nbsp; <?= urldecode($report->os); ?></li>
            <?php endif; ?>

            <?php if($report->country && $report->country !== ''): ?>
                <li class="inline" style="margin-right: 15px;"> <img class="flag flag-<?= strtolower($report->iso); ?>" src="<?php echo $this->assetManager->getAssetUrl($flags, 'blan.gif'); ?>" alt="<?= $report->country;?>"> <?= urldecode($report->country); ?> (<?= long2ip($report->ip_addr); ?>)</li>
            <?php endif; ?>

            <?php if($report->fe_server && $report->fe_server !== ''): ?>
                <li class="inline" style="margin-right: 15px;"> <i class="fa fa-server"></i> <?= $report->fe_server; ?> </li>
            <?php endif; ?>

            <?php if ($report->src_quality && $report->src_quality !==''): ?>
                <li class="inline" style="margin-right: 20px;"> <i class="fa fa-video-camera" aria-hidden="true"></i> <?= $report->src_quality; ?> </li>
            <?php endif; ?>

        </ul>

    </div>
</div>
