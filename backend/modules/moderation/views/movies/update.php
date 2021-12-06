<?php

\backend\assets\MoviesModerationAssets::register($this);

$this->title = $model->title . ' (' . $model->year . ')';
$this->params['breadcrumbs'][] = ['label' => 'Movies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id_movie]];
$this->params['breadcrumbs'][] = 'Moderation';

?>

<script>
    window.can_admin = false;
    <?php if(\Yii::$app->user->can('administrator')): ?>
    window.can_admin = true;
    document.addEventListener('DOMContentLoaded', function () {
        let items = document.querySelectorAll('.admin_only');
        items.forEach(item => {
            item.classList.remove('admin_only');
        });
    });
    <?php endif; ?>

    // Server Used For Playback
    window['edge'] = '<?= $edge->domain;?>';

    <?php if($draft_id !== 0): ?>
    window.draft_id = '<?= $draft_id?>';
    <?php endif; ?>

    // List Of Languages
    window['subtitles_languages'] = JSON.parse('<?php echo addslashes(json_encode($subtitles_languages)); ?>');
    window['subtitles'] = [
        <?php foreach ($subtitles as $index => $subtitle): ?> {
            id: <?= $subtitle['id'];?>,
            is_approved: <?= $subtitle['is_approved'] === '0' ? 'false' : 'true'; ?>,
            is_approved_default: <?= $subtitle['is_approved'] === '0' ? 'false' : 'true'; ?>,
            language: '<?= $subtitle['language']; ?>',
            url: '<?= $subtitle['url']; ?>',
            offset: 0,
            is_editing: false,
            is_playing: false,
            is_deleted: false,
            is_moderated: <?= $subtitle['is_moderated'] === '0' ? 'false' : 'true'; ?>
        }
        <?= $index + 1 !== count($subtitles) ? ',' : '' ?>
        <?php endforeach; ?>
    ];

    window['movie_meta'] = '<?= addslashes($movie_meta); ?>';
    window['protection_data'] = JSON.parse('<?= json_encode($protection_data); ?>');
    window['draft_loaded'] = JSON.parse('<?= addslashes(json_encode($draft)) ?>');

    window.addEventListener('DOMContentLoaded', function () {
        ModerationMoviesUpdate();
    });
</script>

<div class="row">
    <div class="col-sm-12">
        <div class="box">
            <div class="box-body">
                <div class="controls pull-left reconvert-hidden">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                            Actions <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="#apply-torrent-for-movie">Reconvert with Magnet Link / Torrent Upload</a>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li>
                                <a href="/moderation/movies-download-queue/view?id=<?= $model->id_movie; ?>"
                                   target="_blank">View Download Queue #<?= $model->id_movie; ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="rel_title pull-left">
                    <span style="font-weight: normal;">Release Title:</span> <?= $model->rel_title; ?>
                </div>
                <div class="controls pull-right">
                    <a href="/moderation/movies/cancel?id=<?= $model->id_movie; ?>&back_url=<?= $back_url; ?>"
                       type="button" class="btn btn-link">Cancel Moderation
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
                        <label style="display: block;">Quality:</label>
                        <input id="quality_approved" type="checkbox"
                            <?php echo $model->moviesModeration->quality_approved === 1 ? 'checked' : ''; ?>
                               data-default="<?= $model->moviesModeration->quality_approved; ?>" data-toggle="toggle"
                               data-on="Approved"
                               data-off="Not Approved">
                    </div>
                    <div class="form-group" style="display: none;">
                        <label style="display: block;">Subtitles:</label>
                        <input id="finalized_subs" type="checkbox"
                            <?php echo $model->moviesModeration->finalized_subs === 1 ? 'checked' : ''; ?>
                               data-default="<?= $model->moviesModeration->finalized_subs; ?>" data-toggle="toggle"
                               data-on="Finalized"
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
                    <div class="play_quality_switcher"></div>
                    <video id='video_player' class='video-js vjs-lookmovie vjs-default-skin vjs-fluid' controls>
                        <?php if (isset($streamURL[720])): ?>
                            <source label="720p" src="<?= $edge->domain . '/' . $streamURL[720]; ?>"
                                    type="application/x-mpegURL" selected="true">
                        <?php endif; ?>
                        <?php if (isset($streamURL[480])): ?>
                            <source label="480p" src="<?= $edge->domain . '/' . $streamURL[480]; ?>"
                                    type="application/x-mpegURL" <?php echo isset($streamURL[720]) ? '' : 'selected="true"'; ?>>
                        <?php endif; ?>
                        <?php if (isset($streamURL[360])): ?>
                            <source label="360p" src="<?= $edge->domain . '/' . $streamURL[360]; ?>"
                                    type="application/x-mpegURL" <?php echo isset($streamURL[480]) ? '' : 'selected="true"'; ?>>
                        <?php endif; ?>
                        <?php if (isset($streamURL[1080])): ?>
                            <source label="1080p" src="<?= $edge->domain . '/' . $streamURL[1080]; ?>"
                                    type="application/x-mpegURL" <?php echo isset($streamURL[480]) && isset($streamURL[720]) && isset($streamURL[360]) ? '' : 'selected="true"'; ?>>
                        <?php endif; ?>
                        <p class='vjs-no-js'>
                            To view this video please enable JavaScript, and consider upgrading to a web browser that
                            <a href='https://videojs.com/html5-video-support/' target='_blank'>supports HTML5 video</a>
                        </p>
                    </video>
                </div>
                <?php if(false): ?>
                    <div class="subs-offset faded" id="player-controls">
                        <div class="offset-value">Time Shift: {{ offset | MSS }} seconds</div>
                        <div class="timing-controls">
                            <div class="btn-group">
                                <button v-on:click="updateOffset(-1000, id)" type="button" class="btn btn-default btn-xs"
                                        :disabled="url === '' || is_approved === true">
                                    -1s
                                </button>
                                <button v-on:click="updateOffset(-100, id)" type="button" class="btn btn-default btn-xs"
                                        :disabled="url === '' || is_approved === true">
                                    -0.1s
                                </button>
                                <button v-on:click="updateOffset(-10, id)" type="button" class="btn btn-default btn-xs"
                                        :disabled="url === '' || is_approved === true">
                                    -0.01s
                                </button>
                                <button v-on:click="updateOffset(10, id)" type="button" class="btn btn-default btn-xs"
                                        :disabled="url === '' || is_approved === true">
                                    +0.01s
                                </button>
                                <button v-on:click="updateOffset(100, id)" type="button" class="btn btn-default btn-xs"
                                        :disabled="url === '' || is_approved === true">
                                    +0.1s
                                </button>
                                <button v-on:click="updateOffset(1000, id)" type="button" class="btn btn-default btn-xs"
                                        :disabled="url === '' || is_approved === true">
                                    +1s
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="tow-items">
            <!--  Cover Uploader  -->
            <div class="box" id="cover-uploader" style="width: 49%;">
                <div class="box-body">
                    <div class="box-header">
                        <i class="fa fa-upload"></i>
                        <h3 class="box-title">Manual Cover Upload:</h3>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <button v-bind:class="{disabled: uploadInProccess}" v-on:click="UploadClickHandle()"
                                        type="button" class="btn btn-default btn-flat btn-sm">
                                    <span v-if="uploadInProccess">Loading...</span>
                                    <span v-else>Upload</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="upload-list">
                        <div class="alert alert-success alert-dismissible" role="alert" v-if="isSaved">
                            <strong>Success!</strong> Cover successfully saved!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <ul class="todo-list ui-sortable">
                            <li v-if="isVisible">
                                <div v-if="isErrors">
                                    <table class="table table-striped table-bordered">
                                        <tr v-for="(value, name) in coverData">
                                            <td class="text-bold">{{ name }}</td>
                                            <td>{{ value }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div v-else-if="!isSaved">
                                    <div class="m-card">
                                        <div class="m-card__img-wrapper">
                                            <img v-bind:src="coverImage" alt="<?= $model->title; ?>">
                                        </div>
                                        <div class="m-card__meta">
                                            <span class="m-card__title"><?= $model->title; ?> (<?= $model->year; ?>) - <a
                                                        href="https://www.imdb.com/title/tt<?= $model->imdb_id ?>"
                                                        target="_blank">tt<?= $model->imdb_id ?></a></span>
                                        </div>
                                    </div>

                                    <div class="tools">
                                        <div class="btn-group">
                                            <button v-on:click="SaveCover()" type="button" class="btn btn-default"><i
                                                        class="fa fa-save"></i></button>
                                            <button v-on:click="ShowCover()" type="button" class="btn btn-default"><i
                                                        class="fa fa-eye"></i></button>
                                            <button v-on:click="DeleteSubtitle()" type="button" class="btn btn-default"><i
                                                        class="fa fa-trash-o"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>


                        <!-- Show cover image in modal  -->
                        <div class="modal fade" id="coverImageModal" tabindex="-1" role="dialog"
                             aria-labelledby="CoverImage" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Cover Image</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <img v-bind:src="coverImage" alt="<?= $model->title; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <!--  Backdrop Uploader  -->
            <div class="box" id="backdrop-uploader" style="width: 49%;">
                <div class="box-body">
                    <div class="box-header">
                        <i class="fa fa-upload"></i>
                        <h3 class="box-title">Manual Backdrop Upload:</h3>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <button v-bind:class="{disabled: uploadInProccess}" v-on:click="UploadClickHandle()"
                                        type="button" class="btn btn-default btn-flat btn-sm">
                                    <span v-if="uploadInProccess">Loading...</span>
                                    <span v-else>Upload</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="upload-list">
                        <div class="alert alert-success alert-dismissible" role="alert" v-if="isSaved">
                            <strong>Success!</strong> Backdrop successfully saved!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <ul class="todo-list ui-sortable">
                            <li v-if="isVisible">
                                <div v-if="isErrors">
                                    <table class="table table-striped table-bordered">
                                        <tr v-for="(value, name) in backdropData">
                                            <td class="text-bold">{{ name }}</td>
                                            <td>{{ value }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div v-else-if="!isSaved">
                                    <div class="m-card">
                                        <div class="m-card__img-wrapper">
                                            <img v-bind:src="backdropImage" alt="<?= $model->title; ?>">
                                        </div>
                                        <div class="m-card__meta">
                                            <span class="m-card__title"><?= $model->title; ?> (<?= $model->year; ?>) - <a
                                                        href="https://www.imdb.com/title/tt<?= $model->imdb_id ?>"
                                                        target="_blank">tt<?= $model->imdb_id ?></a></span>
                                        </div>
                                    </div>

                                    <div class="tools">
                                        <div class="btn-group">
                                            <button v-on:click="SaveBackdrop()" type="button" class="btn btn-default"><i
                                                        class="fa fa-save"></i></button>
                                            <button v-on:click="ShowBackdrop()" type="button" class="btn btn-default"><i
                                                        class="fa fa-eye"></i></button>
                                            <button v-on:click="DeleteBackdrop()" type="button" class="btn btn-default"><i
                                                        class="fa fa-trash-o"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>


                        <!-- Show cover image in modal  -->
                        <div class="modal fade" id="coverImageModal" tabindex="-1" role="dialog"
                             aria-labelledby="BackdropImage" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle">Cover Image</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <img v-bind:src="backdropImage" alt="<?= $model->title; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="metadata">
        <div class="box">
            <div class="box-body">
                <h4>Metadata:</h4>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label style="display: block;">Title:</label>
                                    <input type="text" value="<?= $model->title; ?>" default-value="<?= $model->title; ?>"
                                           class="form-control" id="title" name="title">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label style="display: block;">Year:</label>
                                    <input type="text" value="<?= $model->year; ?>" class="form-control"
                                           default-value="<?= $model->year; ?>" id="year" name="year">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="description">Description:</label>
                                    <textarea default-value="<?= $model->description; ?>" class="form-control"
                                              name="description"
                                              id="description" rows="10"><?= $model->description; ?></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Homepage:</label>
                                    <input type="text" class="form-control" value="<?= $model->homepage; ?>"
                                           default-value="<?= $model->homepage; ?>" id="homepage" name="homepage">
                                </div>
                            </div>
                        </div><!-- .row -->
                    </div><!-- .col-sm-6 -->
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Country ISO, 639-1:</label>
                                    <input type="text" class="form-control" value="<?= $model->country; ?>"
                                           default-value="<?= $model->country; ?>" id="country" name="country">
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Duration, seconds:</label>
                                    <input type="text" class="form-control" value="<?= $model->duration; ?>"
                                           default-value="<?= $model->duration; ?>" id="duration" name="duration">
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Trailer(Youtube video key):</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?= $model->youtube; ?>"
                                               default-value="<?= $model->youtube; ?>" name="youtube" id="youtube">
                                        <span class="input-group-btn">
											<a href="https://www.youtube.com/watch?v=<?= $model->youtube; ?>"
                                               target="_blank"
                                               class="btn btn-danger" type="button"> <i class="fa fa-play"></i> Youtube</a>
										</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>IMDb ID:</label>
                                    <div class="input-group">
                                        <span class="input-group-addon">tt</span>
                                        <input type="text" class="form-control" value="<?= $model->imdb_id; ?>"
                                               default-value="<?= $model->imdb_id; ?>" name="imdb_id" id="imdb_id">
                                        <span class="input-group-btn">
											<a href="https://imdb.com/title/tt<?= $model->imdb_id; ?>" target="_blank"
                                               class="btn btn-primary"
                                               type="button">Visit IMDb</a>
										</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>TMDb ID:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?= $model->tmdb_prefix; ?>"
                                               default-value="<?= $model->tmdb_prefix; ?>" name="tmdb_prefix"
                                               id="tmdb_prefix">
                                        <span class="input-group-btn">
											<a href="https://www.themoviedb.org/movie/<?= $model->tmdb_prefix; ?>"
                                               target="_blank"
                                               class="btn btn-primary" type="button">Visit TMDb</a>
										</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Budget:</label>
                                    <input type="text" class="form-control" value="<?= $model->budget; ?>"
                                           default-value="<?= $model->budget; ?>" name="budget" id="budget">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Quality:</label>
                                    <select class="form-control input-sm" name="flag_quality" id="flag_quality">
                                        <option <?= (int)$model->flag_quality == 8 ? 'selected' : ''; ?> value="8">
                                            1080p
                                        </option>
                                        <option <?= (int)$model->flag_quality == 7 ? 'selected' : ''; ?> value="7">
                                            720p
                                        </option>
                                        <option <?= (int)$model->flag_quality < 7 ? 'selected' : ''; ?> value="6">LQ
                                        </option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div><!-- .col-sm-6 -->
                </div><!-- .row -->

            </div>
        </div>
    </section>
</div>
<div class="hidden" id="SubtitlesDropzone"></div>
<div class="hidden" id="CoverDropzone"></div>
<div class="hidden" id="BackdropDropzone"></div>

<div class="hidden">
    <form id="draft-form" action="/moderation/movies/save" method="post">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
               value="<?= \Yii::$app->request->csrfToken; ?>">
        <input type="hidden" name="id_media" value="<?= $model->id_movie; ?>">
        <input type="hidden" name="is_draft" value="1">
        <input type="hidden" name="execute-now" value="0">
        <input type="hidden" name="draft_title"
               value="<?= $model->title; ?> (<?= $model->year; ?>) - tt<?= $model->imdb_id; ?>">
        <input id="draft-data" type="text" name="data" value="e30=">
    </form>
</div>

<div class="modal modal-success fade in" id="movies-torrent-uploader" v-if="inProgress"
     v-bind:style="{ display: inProgress ? 'block' : 'none' }">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="lds-wrapper" v-if="isAjaxing">
                <div class="lds-ripple">
                    <div></div>
                    <div></div>
                </div>
            </div>
            <div class="modal-header">
                <button v-on:click="inProgress = false;" type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Reconvert with Magnet Link / Torrent Upload</h4>
            </div>
            <div class="modal-body">
                <form action="/moderation/movies/reconvert-torrent?id=<?= $model->id_movie; ?>" method="post"
                      id="torrent-upload">
                    <input type="hidden" name="<?= \Yii::$app->request->csrfParam; ?>"
                           value="<?= \Yii::$app->request->csrfToken; ?>"/>
                    <div class="form-group">
                        <label>Release Title:</label>
                        <input type="text" name="release-title" class="form-control" placeholder="Enter ..."
                               v-model="releaseTitleModel">
                    </div>
                    <div class="form-group">
                        <label>Magnet Link</label>
                        <textarea class="form-control" name="content" rows="3" placeholder="Enter ..."
                                  v-model="uploadContentModel"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline"
                        :disabled="releaseTitleModel === '' || uploadContentModel === ''" v-on:click="submitTorrent">
                    Reconvert
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
