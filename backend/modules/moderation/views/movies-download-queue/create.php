<?php

use backend\assets\VueBundle;
use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\moderation\models\MoviesDownloadQueue */

$this->title = 'Add Movie To Download Queue';
$this->params['breadcrumbs'][] = ['label' => 'Movies Download Queue', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

VueBundle::register($this);
?>
<script>

    window.addEventListener('DOMContentLoaded', function () {
        window.vueApp = new Vue({
            el: '#add-new-movie',
            name: 'Add New Movie',
            data() {
                return {
                    forceTorrent: true,
                    IMDbID: '<?= Yii::$app->request->get('imdb_id', ''); ?>',
                    title: '',
                    year: '',
                    originalLanguage: '',
                    releaseTitleModel: '',
                    uploadContentModel: '',

                }
            },
            computed: {
                IMDbFormatted: function () {
                    const regex = /tt[0-9]+/gm;
                    let m = regex.exec(this.IMDbID);
                    if (typeof(m['0']) !== 'undefined') {
                        return m['0'];
                    }

                    return '';
                }
            },
            methods: {
                onFileChange: function (e) {
                    this.isAjaxing = true;
                    let files = e.target.files || e.dataTransfer.files;
                    let reader = new FileReader();
                    let vm = this;

                    reader.onload = (e) => {
                        vm.uploadContentBin = e.target.result;
                        vm.uploadContentModel = vm.uploadContentBin;
                        if (typeof (files[0].name) !== 'undefined' && files[0].name !== '') {
                            vm.releaseTitleModel = files[0].name;
                        }
                        vm.isAjaxing = false;
                    };
                    reader.readAsDataURL(files[0]);
                },
                getDetails: function () {

                    let vm = this;

                    let btn = $('#find_button');
                    btn.button('loading');

                    this.IMDbID = this.IMDbFormatted;

                    let settings = {
                        "async": true,
                        "crossDomain": true,
                        "url": 'https://api.themoviedb.org/3/find/' + this.IMDbFormatted + '?api_key=4e5bcfc0b906b18c8b9daf95946b36fa&language=en-US&external_source=imdb_id',
                        "method": "GET",
                        "headers": {},
                        "data": {}
                    };

                    $.ajax(settings).done(function (response) {
                        if (response.movie_results.length > 0) {
                            vm.title = response.movie_results[0].title;
                            vm.originalLanguage = response.movie_results[0].original_language;
                            vm.year = typeof (response.movie_results[0].release_date.split('-')['0']) !== 'undefined' ? response.movie_results[0].release_date.split('-')['0'] : '';
                        }
                        btn.button('reset');
                    }).fail(function () {
                        btn.button('reset');
                    });

                }
            },
            mounted: function () {
                if (this.IMDbID !== '') {
                    this.getDetails()
                }
            }
        });
    });

</script>
<div class="movies-download-queue-create" id="add-new-movie">
    <div class="movies-download-queue-form">
        <form action="/moderation/movies-download-queue/create" method="post">
            <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
                   value="<?= Yii::$app->request->csrfToken; ?>">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label style="display: block;">IMDb ID</label>
                        <div class="input-group">
                            <input v-model="IMDbID" name="IMDbID" type="text" class="form-control">
                            <div class="input-group-btn">
                                <button id="find_button" type="button" class="btn btn-primary"
                                        v-on:click="getDetails()">Find
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label style="display: block;">Title</label>
                        <input v-model="title" type="text" name="title" class="form-control">
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label style="display: block;">Year</label>
                        <input v-model="year" type="text" name="year" class="form-control">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label style="display: block;">Original Language (ISO 639-1)</label>
                        <input v-model="originalLanguage" type="text" name="original_language" class="form-control">
                    </div>
                </div>
            </div>
            <div class="row" v-if="forceTorrent" style="display: none;"
                 v-bind:style="{ display: !forceTorrent ? 'none' : 'block' }">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Release Title:</label>
                        <input type="text" name="release-title" class="form-control" placeholder="Enter ..."
                               v-model="releaseTitleModel">
                    </div>
                    <div class="form-group">
                        <label>Magnet Link / Torrent Content</label>
                        <textarea class="form-control" name="content" rows="3" placeholder="Enter ..."
                                  v-model="uploadContentModel"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="torrentFileUpload">Torrent File Upload</label>
                        <input type="file" id="torrentFileUpload" @change="onFileChange">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="checkbox pull-right">
                        <label>
                            <input type="checkbox" v-model="forceTorrent">
                            Force Torrent Download
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <button type="submit" class="btn bg-olive btn-flat pull-right">Add Movie</button>
                </div>
            </div>
        </form>
    </div>
</div>
