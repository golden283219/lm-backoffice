<?php


/* @var $this yii\web\View */
/* @var $model common\models\queue\Shows */

$this->title = 'Add TV Show';
$this->params['breadcrumbs'][] = ['label' => 'Shows', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\backend\assets\VueBundle::register($this);

?>
<form class="hidden" action="/site/redirect" id="redirect-form" method="get">
    <input type="text" name="type">
    <input type="text" name="message">
    <input type="text" name="url" value="/moderation/shows-download-queue">
</form>
<div class="shows-create">
    <div class="movies-download-queue-create" id="add-new-tvshow">
        <div class="progress-overlay hidden" v-if="is_inserting_show">
            <div class="progress-inner-container">
                <h2>Adding TV Show: {{title}}</h2>
                <div class="progress active">
                    <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar"
                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    </div>
                </div>
            </div>
        </div>
        <div class="movies-download-queue-form">
            <form action="/moderation/movies-download-queue/create" method="post">
                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
                       value="<?= \Yii::$app->request->csrfToken; ?>">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group" v-bind:class="{'has-error': errors.imdb_id.length > 0}">
                            <label style="display: block;">IMDb ID</label>
                            <div class="input-group">
                                <input v-model="imdb_id" name="imdb_id" type="text" v-on:keyup="performInputChecks" class="form-control"
                                       placeholder="tt0012353">
                                <div class="input-group-btn">
                                    <button id="find_button" type="button" class="btn btn-primary"
                                            v-on:click="getDetails()">Find
                                    </button>
                                </div>
                            </div>
                            <p class="help-block" v-if="errors.imdb_id.length > 0">{{ errors.imdb_id }}</p>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group" v-bind:class="{'has-error': errors.title.length > 0}">
                            <label style="display: block;">Title</label>
                            <input v-model="title" type="text" name="title" v-on:keyup="performInputChecks" class="form-control">
                            <p class="help-block">{{ errors.title }}</p>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group" v-bind:class="{'has-error': errors.first_air_date.length > 0}">
                            <label style="display: block;">First Air Date</label>
                            <input v-model="first_air_date" v-on:keyup="performInputChecks" type="text" name="first_air_date" class="form-control"
                                   placeholder="2015-05-27">
                            <p class="help-block">{{ errors.first_air_date }}</p>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group" v-bind:class="{'has-error': errors.original_language.length > 0}">
                            <label style="display: block;">Original Language</label>
                            <input v-model="original_language" type="text" name="original_language" v-on:keyup="performInputChecks" class="form-control" placeholder="en">
                            <p class="help-block">{{ errors.original_language }}</p>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Priority</label>
                            <input type="number" min="0" max="9" class="form-control" v-model="priority">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="btn bg-olive btn-flat pull-right" v-on:click="insertShow()">ADD</div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    window.addEventListener('DOMContentLoaded', function () {
        window.vueApp = new Vue({
            el: '#add-new-tvshow',
            name: 'torrentUploader',
            data() {
                return {
                    errors: {
                        imdb_id: '',
                        tvmaze_id: '',
                        title: '',
                        first_air_date: '',
                        original_language: ''
                    },
                    is_inserting_show: false,
                    imdb_id: '',
                    title: '',
                    first_air_date: '',
                    original_language: '',
                    episode_duration: 0,
                    priority: 0
                }
            },
            methods: {
                doRedirectWith: function (type, message) {
                    $('#redirect-form [name="type"]').val(type)
                    $('#redirect-form [name="message"]').val(message)
                    $('#redirect-form').submit()
                },
                discoverEpisodes: function () {
                    return axios.get(yii2app.apiBaseURL + '/v1/imdb-datasets/' + this.imdb_id + '/episodes')
                },
                getShowDetails: function () {
                    return axios.get(yii2app.apiBaseURL + '/v1/imdb-datasets/' + this.imdb_id + '/details')
                },
                findEnTitle: function (akas) {
                    var enAkas = akas.filter(aka => aka.language === 'en');
                    enAkas = enAkas.sort(function (a, b) {
                        return a.ordering - b.ordering;
                    });

                    if (enAkas.length > 0) {
                        return enAkas[0].title;
                    }

                    return null;
                },
                loadData: function (response) {
                    const metadata = response[0].data;

                    this.original_language = metadata.original_language;
                    this.title = this.findEnTitle(metadata.akas) ?? metadata.primary_title;
                    this.first_air_date = [metadata.start_year, '01', '01'].join('-');
                    this.episode_duration = metadata.runtime_minutes;
                },
                getDetails: function () {
                    // get tvmaze details by imdb_id
                    find_button = $('#find_button')
                    find_button.button('loading')

                    let promises = [this.getShowDetails()];
                    Promise.all(promises).then((response) => {
                        this.loadData(response)
                        find_button.button('reset')
                        this.performInputChecks();
                    }).catch((err) => {
                        console.dir(err);
                        find_button.button('reset')
                        this.performInputChecks();
                    })
                },
                performInputChecks() {
                    if (/tt\d{4,12}/gm.test(this.imdb_id) === false) {
                        this.errors.imdb_id = 'Wrong or empty IMDb Id';
                    } else {
                        this.errors.imdb_id = '';
                    }

                    if (this.title.length < 1) {
                        this.errors.title = 'Title Can Not Be Empty';
                    } else {
                        this.errors.title = '';
                    }

                    if (/\d{4}-([0][1-9]|10|11|12)-(([0,1,2][0-9])|3[0,1])/gm.test(this.first_air_date) === false) {
                        this.errors.first_air_date = 'Wrong first air date';
                    } else {
                        this.errors.first_air_date = '';
                    }

                    if (this.original_language.length < 2) {
                        this.errors.original_language = 'Wrong Original Language';
                    } else {
                        this.errors.original_language = '';
                    }
                },
                insertShow: function () {
                    const api_insert_url = yii2app.apiBaseURL + '/v1/shows-download-queue/insert-show?o=json';

                    this.performInputChecks();

                    for (let key in this.errors) {
                        if (this.errors.hasOwnProperty(key) && this.errors[key].length > 0) {
                            return false;
                        }
                    }

                    this.is_inserting_show = true

                    axios.post(api_insert_url, {
                        imdb_id: this.imdb_id,
                        title: this.title,
                        first_air_date: this.first_air_date,
                        original_language: this.original_language,
                        episode_duration: this.episode_duration
                    }).then((response) => {
                        const result = response.data
                        //@todo get message from server
                        if (result.status === 0) {
                            return this.doRedirectWith('error', result.message)
                        }

                        this.insertEpisodes(result.show.id_tvshow).then((response) => {
                            this.doRedirectWith('success', 'Updated Show: ' + this.title + ' \/ Updated episodes: ' + count + ' \/ Priority: ' + this.priority)
                        })
                    }).catch((err) => {
                        alert('Error Adding TV Show. Please Try After Page Refresh.');
                    })
                },
                insertEpisodes: function (id_tvshow) {
                    const api_insert_url = yii2app.apiBaseURL + '/v1/shows-download-queue/insert-episode?o=json'

                    return new Promise(async (resolve, reject) => {
                        try {
                            const response = await this.discoverEpisodes();
                            const episodes = response.data;
                            const promises = [];
                            for (let key in episodes) {
                                if (episodes.hasOwnProperty(key)) {
                                    for (let i = 0; i < episodes[key].length; i++) {
                                        promises.push(axios.post(api_insert_url, {
                                            'episode': episodes[key][i],
                                            'id_tvshow': id_tvshow,
                                            'priority': this.priority
                                        }).catch(err => { return err }));
                                    }
                                }
                            }

                            Promise.all(promises).then(values => {
                                let added = 0,
                                    failed = 0;
                                for (let index = 0; values.length > index; index++) {
                                    console.dir(values[index]);
                                    if (typeof(values[index].statusText) !== 'undefined' && values[index].statusText === 'OK') {
                                        added++;
                                    } else {
                                        failed++;
                                    }
                                }

                                this.doRedirectWith('success', 'Show added: ' + this.title + ' \/ Added episodes: ' + added + ' \/ Priority: ' + this.priority)
                            })
                        } catch (e) {
                            alert(e);
                            this.doRedirectWith('error', 'Not added, please try again later');
                        }
                    })
                }
            },
            mounted () {
                this.performInputChecks()
            },
            created() {
                let elem = document.querySelector('.progress-overlay')
                if (typeof (elem) !== 'undefined') {
                    elem.classList.remove('hidden')
                }
            }
        });
    });

</script>
