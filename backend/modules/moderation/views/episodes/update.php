<?php

use backend\assets\EpisodesUpdate;
use backend\assets\VideoPlaybackAsset;
use backend\assets\VueBundle;
use backend\widgets\EpisodeApplyTorrent;
use common\helpers\Html;

VideoPlaybackAsset::register($this);
VueBundle::register($this);
EpisodesUpdate::register($this);

$this->title = 'Moderation: ' . $model->show->title . ' (' . $model->show->year . ') - Season ' . $model->season . ' Episode ' . $model->episode;

$this->params['breadcrumbs'][] = ['label' => 'Shows Episodes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Moderation: Episode #' . $model->id;

?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        /*
         * Listen for Torrent Upload Request
         */
        document.querySelector('a[href="#apply-torrent-for-show"]').onclick = function (e) {
            e.preventDefault();
            window.dispatchEvent(new CustomEvent('handle-torrent-upload', {
                detail: {
                    id_episode: <?= $model->id; ?>,
                    subtitles_languages: JSON.parse('<?php echo addslashes(json_encode($subtitles_languages)); ?>')
                }
            }));
        }
    });

    window.__URL_API = '<?= env('API_HOST_INFO'); ?>';

    window['edge'] = '<?= $edge->domain;?>';
    window['episode'] = JSON.parse('<?= addslashes(json_encode($model->attributes));?>');
    window.subtitles_updates = [];
    window['subtitles_languages'] = JSON.parse('<?php echo addslashes(json_encode($subtitles_languages)); ?>');
    window['protection_data'] = JSON.parse('<?= addslashes(json_encode($protection_data)); ?>');
    window.subtitles = [
        <?php foreach ($subtitles as $index => $subtitle): ?> {
            id: <?= $subtitle['id'];?>,
            is_approved: <?= $subtitle['is_approved'] === '0' ? 'false' : 'true'; ?>,
            is_approved_default: <?= $subtitle['is_approved'] === '0' ? 'false' : 'true'; ?>,
            languageName: '<?= trim($subtitle['languagename']); ?>',
            storagePath: '<?= trim($subtitle['storagepath']); ?>',
            isoCode: '<?= trim($subtitle['isocode']); ?>',
            offset: 0,
            is_editing: false,
            is_playing: false,
            is_deleted: false,
            is_moderated: <?= $subtitle['is_moderated'] === '0' ? 'false' : 'true'; ?>
        }

        <?= $index + 1 !== count($subtitles) ? ',' : '' ?>
        <?php endforeach; ?>
    ];
</script>

<?php echo EpisodeApplyTorrent::widget([]); ?>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-body">
                <div class="controls pull-left reconvert-hidden">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                            Actions <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <?php echo Html::a('Reconvert Using Magnet', '#', [
                                    'id' => 'apply-torrent-for-show',
                                    'data-title' => $model->show->title,
                                    'data-year' => $model->show->first_air_date,
                                    'data-season' => $model->season,
                                    'data-episode' => $model->episode,
                                    'data-id' => !empty($shows_meta) ? $shows_meta->id_meta : -1
                                ]); ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="rel_title pull-left reconvert-hidden">
                    <span style="font-weight: normal;">Release Title:</span> <?= $model->rel_title; ?>
                </div>
                <div class="reconvert-only pull-left"><span style="font-weight: bold; font-size: 24px;">Episode is Set To Reconvert!</span>
                </div>
                <div class="controls pull-right">
                    <a href="/moderation/episodes/cancel?id=<?= $model->id; ?>&back_url=<?= $back_url; ?>" type="button"
                       class="btn btn-link">
                        Cancel Draft
                    </a>
                    <button id="submit-execute-draft" type="button" class="btn btn-primary">
                        Save <i class="fa fa-check-square-o" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="moderation-zone-wrapper">
    <section class="operation-zone">
        <div class="box">
            <div class="box-body">
                <form class="form-inline">
                    <div class="form-group">
                        <label style="display: block;">Status:</label>
                        <input id="quality_approved" type="checkbox"
                            <?php echo $model->quality_approved === 1 ? 'checked' : ''; ?>
                               data-default="<?= $model->quality_approved; ?>" data-toggle="toggle" data-on="Approved"
                               data-off="Not Approved">
                    </div>
                    <div class="form-group" style="display: none !important;">
                        <label style="display: block;">Subtitles:</label>
                        <input id="finalized_subs" type="checkbox"
                            <?php echo $model->finalized_subs === 1 ? 'checked' : ''; ?>
                               data-default="<?= $model->finalized_subs; ?>" data-toggle="toggle" data-on="Finalized"
                               data-off="Not Finalized">
                    </div>
                    <div class="form-group pull-right">
                        <label style="display: block;">Is Active:</label>
                        <input id="is_active" type="checkbox" <?php echo $model->is_active === 1 ? 'checked' : ''; ?>
                               data-default="<?= $model->is_active; ?>" data-toggle="toggle" data-on="Active"
                               data-off="Inactive">
                    </div>
                </form>
                <div id="player-zone" class="player-zone">
                    <div class="play_quality_switcher">
                    </div>
                    <video id='video_player' class='video-js vjs-lookmovie vjs-default-skin vjs-fluid' controls>

                        <?php if (isset($model->storage['720'])): ?>
                            <source label="720p"
                                    src="<?= $edge->domain . "/{$protection_data->hash}/{$protection_data->expires}/{$model->shard}/" . $model->storage['720']; ?>"
                                    type="application/x-mpegURL" selected="true">
                        <?php endif; ?>

                        <?php if (isset($model->storage['480'])): ?>
                            <source label="480p"
                                    src="<?= $edge->domain . "/{$protection_data->hash}/{$protection_data->expires}/{$model->shard}/" . $model->storage['480']; ?>"
                                    type="application/x-mpegURL" <?php echo isset($model->storage['720']) ? '' : 'selected="true"'; ?>>
                        <?php endif; ?>

                        <?php if (isset($model->storage['360'])): ?>
                            <source label="360p"
                                    src="<?= $edge->domain . "/{$protection_data->hash}/{$protection_data->expires}/{$model->shard}/" . $model->storage['360']; ?>"
                                    type="application/x-mpegURL" <?php echo isset($model->storage['480']) ? '' : 'selected="true"'; ?>>
                        <?php endif; ?>

                        <?php if (isset($model->storage['1080'])): ?>
                            <source label="1080p"
                                    src="<?= $edge->domain . "/{$protection_data->hash}/{$protection_data->expires}/{$model->shard}/" . $model->storage['1080']; ?>"
                                    type="application/x-mpegURL" <?php echo isset($model->storage['480']) && isset($model->storage['720']) && isset($model->storage['360']) ? '' : 'selected="true"'; ?>>
                        <?php endif; ?>

                        <p class='vjs-no-js'>
                            To view this video please enable JavaScript, and consider upgrading to a web browser that
                            <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                        </p>
                    </video>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="hidden" id="SubtitlesDropzone"></div>
<div class="hidden">
    <form id="draft-form" action="/moderation/episodes/save?back_url=<?= $back_url; ?>" method="post">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
               value="<?= \Yii::$app->request->csrfToken; ?>">
        <input type="hidden" name="id_media" value="<?= $model->id; ?>">
        <input type="hidden" name="is_draft" value="1">
        <input type="hidden" name="execute-now" value="0">
        <input type="hidden" name="draft_title" value="<?= $model->show->title; ?> (<?= $model->show->year; ?>) - tt<?= $model->show->imdb_id; ?>">
        <input id="draft-data" type="text" name="data" value="e30=">
    </form>
</div>
