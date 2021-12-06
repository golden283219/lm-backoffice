function ModerationMoviesUpdate() {
    window.draft = [];

    window.movie_meta_obj = JSON.parse(movie_meta);

    let playerOptions = {
        controlBar: {
            children: [
                'playToggle',
                'progressControl',
                'volumePanel',
                'qualitySelector',
                'fullscreenToggle',
            ],
            volumePanel: {
                inline: true
            }
        },
        html5: {
            hls: {
                overrideNative: true
            }
        }
    };

    function initNotApprovedList() {
        window.NotApprovedSubtitlesVue = new Vue({
            el: '#subtitles-not-approved',
            name: 'Not Approved',
            data() {
                return {
                    subtitles: window.subtitles,
                    can_admin: window.can_admin
                }
            },
            computed: {
                subtitles_filtered: function () {
                    let subtitles = [];
                    this.subtitles.forEach(item => {
                        if (!item.is_approved) {
                            subtitles.push(item);
                        }
                    });
                    return subtitles;
                }
            },
            filters: {
                MSS: function (value) {
                    return (value / 1000).toFixed(2);
                }
            },
            methods: {
                DeleteSubtitle: function (id) {
                    let vm = this;
                    this.subtitles.forEach((subtitle, index) => {
                        if (subtitle.id === id) {
                            if (subtitle.is_playing) {
                                subtitle.is_playing = false;
                                window.dispatchEvent(new Event('stopSubtitle'));
                            }
                            if (subtitle.is_approved_default === false && subtitle.is_moderated === false) {
                                $.get('/moderation/movies/subtitle-final-delete?id_subtitle=' + id).done((response) => {
                                    if (response.success === true) {
                                        vm.subtitles.splice(index, 1);
                                    }
                                }).fail(() => {
                                    alert('error deleting subtitle');
                                });
                            } else {
                                subtitle.is_deleted = !subtitle.is_deleted;
                            }
                        }
                    });
                },
                approveSubtitle: function (id) {
                    this.subtitles.forEach(subtitle => {
                        if (subtitle.id === id) {
                            subtitle.is_approved = true;
                        }
                    });
                },
                toggleSubtitlesPlay: function (id) {

                    let selectedSubtitle = null;
                    this.subtitles.forEach(subtitle => {
                        if (subtitle.id === id) {
                            selectedSubtitle = subtitle;
                            subtitle.is_playing = true;
                        } else {
                            subtitle.is_playing = false;
                        }
                    });

                    if (selectedSubtitle !== null) {

                        window.dispatchEvent(new CustomEvent('playSubtitle', {
                            'detail': {
                                id: selectedSubtitle.id,
                                url: 'https://lookmovie.ag' + window['movie_meta_obj'].shard_url + Date.now() + '/' + selectedSubtitle.url,
                                language: selectedSubtitle.language,
                                is_approved: selectedSubtitle.is_approved_default,
                                offset: selectedSubtitle.offset
                            }
                        }));

                    }

                },
            },
            created() {
                document.querySelector('#subtitles-approved').classList.remove('faded');
            }
        });
    }

    function initApprovedList() {
        window['ApprovedSubtitlesVue'] = new Vue({
            el: '#subtitles-approved',
            name: 'Subtitles Approved',
            data() {
                return {
                    subtitles: window['subtitles'],
                    can_admin: window['can_admin']
                }
            },
            computed: {
                subtitles_filtered: function () {
                    let subtitles = [];
                    this.subtitles.forEach(item => {
                        if (item.is_approved) {
                            subtitles.push(item);
                        }
                    });
                    return subtitles;
                }
            },
            methods: {
                unApproveSubtitle: function (id) {
                    this.subtitles.forEach(subtitle => {
                        if (subtitle.id === id) {
                            subtitle.is_approved = false;
                        }
                    });
                },
                DeleteSubtitle: function (id) {
                    let vm = this;
                    this.subtitles.forEach((subtitle, index) => {
                        if (subtitle.id === id) {

                            if (subtitle.is_playing) {
                                subtitle.is_playing = false;
                                window.dispatchEvent(new Event('stopSubtitle'));
                            }

                            if (subtitle.is_approved_default === false) {
                                vm.subtitles.splice(index, 1);
                                $.get('/moderation/movies/subtitle-final-delete?id_subtitle=' + id).fail(() => {
                                    alert('error deleting subtitle');
                                });
                            } else {
                                subtitle.is_deleted = !subtitle.is_deleted;
                            }
                        }
                    });
                },
                toggleSubtitlesPlay: function (id) {

                    let selectedSubtitle = null;
                    this.subtitles.forEach(subtitle => {
                        if (subtitle.id === id) {
                            selectedSubtitle = subtitle;
                            subtitle.is_playing = true;
                        } else {
                            subtitle.is_playing = false;
                        }
                    });

                    if (selectedSubtitle !== null) {

                        window.dispatchEvent(new CustomEvent('playSubtitle', {
                            'detail': {
                                id: selectedSubtitle.id,
                                url: 'https://lookmovie.ag' + window['movie_meta_obj'].shard_url + Date.now() + '/' + selectedSubtitle.url,
                                language: selectedSubtitle.language,
                                is_approved: selectedSubtitle.is_approved_default,
                                offset: selectedSubtitle.offset
                            }
                        }));

                    }

                },
            }
        });
    }

    function initPlayerController() {
        window['PlayerController'] = new Vue({
            el: '#player-controls',
            name: 'PlayerController',
            data() {
                return {
                    id: null,
                    isPlaying: false,
                    is_approved: false,
                    language: '',
                    offset: 0,
                    url: ''
                }
            },
            filters: {
                MSS: function (value) {
                    return (value / 1000).toFixed(2);
                }
            },
            methods: {
                playSubtitle: function () {
                    // Start Playing If Video Not Started Yet
                    if (!window.videoJS.hasStarted()) {
                        window.videoJS.play();
                    }

                    //Remove All Text Tracks
                    let currentTracks = window.videoJS.remoteTextTracks();
                    currentTracks.tracks_.forEach((track, index) => {
                        window.videoJS.removeRemoteTextTrack(track);
                    });

                    //Add selected subtitle
                    let addTrack = window.videoJS.addRemoteTextTrack({
                        kind: 'Captions',
                        label: this.language,
                        src: this.url
                    }, false);

                    addTrack.track.on('loadeddata', () => {
                        addTrack.track.cues_.forEach(item => {
                            item.startTime += this.offset / 1000;
                            item.endTime += this.offset / 1000;
                        });
                    });

                    // Set Track Showing
                    currentTracks.tracks_.forEach((track, index) => {
                        currentTracks[index].mode = 'showing';
                    });
                },
                updateOffset: function (value, id) {

                    this.offset += value;

                    let currentTracks = window.videoJS.remoteTextTracks();
                    currentTracks.tracks_['0'].cues_.forEach(item => {
                        item.endTime += value / 1000;
                        item.startTime += value / 1000;
                    });

                    if (typeof (this.id) !== 'undefined') {
                        window['subtitles'].forEach(subtitle => {
                            if (subtitle.id === this.id) {
                                subtitle.offset += value;
                            }
                        });
                    } else {
                        window['subtitlesVue'].offset = this.offset;
                    }

                    window.videoJS.textTrackDisplay.updateDisplay();
                },
            },
            created() {
                document.querySelector('#player-controls').classList.remove('faded');

                window.addEventListener('playSubtitle', (event) => {
                    this.language = event.detail.language;
                    this.url = event.detail.url;
                    this.offset = event.detail.offset;
                    this.is_approved = event.detail.is_approved;
                    this.id = event.detail.id;
                    this.playSubtitle();
                });

                window.addEventListener('stopSubtitle', () => {

                    this.offset = 0;
                    this.id = null;
                    this.url = '';
                    this.is_approved = false;
                    this.language = '';

                    //Remove All Text Tracks
                    let currentTracks = window.videoJS.remoteTextTracks();
                    currentTracks.tracks_.forEach((track, index) => {
                        window.videoJS.removeRemoteTextTrack(track);
                    });

                });
            }
        });
    }

    function initTorrentUploader() {
        window['torrentUploader'] = new Vue({
            el: '#movies-torrent-uploader',
            name: 'MoviesTorrentUploader',
            data() {
                return {
                    inProgress: false,
                    isAjaxing: false,
                    uploadContentModel: '',
                    releaseTitleModel: '',
                    uploadContentBin: null
                }
            },
            methods: {
                submitTorrent: function () {
                    document.getElementById('torrent-upload').submit();
                },
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

                }
            },
            created() {
                window.addEventListener('handle-torrent-upload', () => {
                    this.inProgress = true;
                    this.uploadContentModel = '';
                    this.releaseTitleModel = '';
                });

            }
        });
    }

    function initCoverUploader() {
        window['coverVue'] = new Vue({
            el: '#cover-uploader',
            name: 'CoverUploader',
            data() {
                return {
                    uploadInProccess: false,
                    isVisible: false,
                    isErrors: false,
                    isSaved: false,
                    coverImage: '/img/poster_placeholder.png',
                    coverData: [],
                    movie_meta: JSON.parse(window['movie_meta']),
                    saveUrl: '/moderation/movies/upload-cover'
                }
            },
            created() {
                document.querySelector('#cover-uploader').classList.remove('faded');
            },
            methods: {
                SaveCover: function () {
                    const _this = this;

                    $.get(this.saveUrl, {
                        movie_id: this.movie_meta.id_movie,
                        poster: this.coverData.path
                    }, function (data, textStatus) {
                        if (data.success) {
                            _this.isSaved = true;
                            _this.isVisible = false;
                        } else {
                            alert(data.message);
                        }
                        1
                    });
                },
                DeleteSubtitle: function () {
                    this.isVisible = false;
                },
                ShowCover: function () {
                    $("#coverImageModal").modal('show');
                },
                UploadClickHandle: function () {

                    let DropZone = document.querySelector("#CoverDropzone");

                    while (DropZone.firstChild) {
                        DropZone.removeChild(DropZone.firstChild);
                    }

                    let fileInput = document.createElement('input');
                    fileInput.type = 'file';

                    DropZone.appendChild(fileInput);

                    fileInput.onchange = () => {
                        let req = new XMLHttpRequest();
                        let formData = new FormData();

                        this.uploadInProccess = true;

                        formData.append("image", fileInput.files[0]);
                        req.open("POST", "/cover-process/");
                        req.send(formData);
                        req.onreadystatechange = () => {
                            if (req.readyState === 4) {
                                const response = JSON.parse(req.response);
                                console.log(response);
                                this.uploadInProccess = false;
                                this.isVisible = true;

                                if (!response.success) {
                                    this.isErrors = true;
                                } else {
                                    this.isErrors = false;
                                    this.coverImage = response.image
                                }

                                this.coverData = response;
                            }
                        };
                    };
                    fileInput.click();
                },
            }
        })
    }

    function initBackdropUploader() {
        window['backdropVue'] = new Vue({
            el: '#backdrop-uploader',
            name: 'BackdropUploader',
            data() {
                return {
                    uploadInProccess: false,
                    isVisible: false,
                    isErrors: false,
                    isSaved: false,
                    backdropImage: '/img/poster_placeholder.png',
                    backdropData: [],
                    movie_meta: JSON.parse(window['movie_meta']),
                    saveUrl: '/moderation/movies/upload-backdrop'
                }
            },
            created() {
                document.querySelector('#backdrop-uploader').classList.remove('faded');
            },
            methods: {
                SaveBackdrop: function () {
                    const _this = this;

                    $.get(this.saveUrl, {
                        movie_id: this.movie_meta.id_movie,
                        backdrop: this.backdropData.path
                    }, function (data, textStatus) {
                        if (data.success) {
                            _this.isSaved = true;
                            _this.isVisible = false;
                        } else {
                            alert(data.message);
                        }
                        1
                    });
                },
                DeleteBackdrop: function () {
                    this.isVisible = false;
                },
                ShowBackdrop: function () {
                    $("#backdropImageModal").modal('show');
                },
                UploadClickHandle: function () {

                    let DropZone = document.querySelector("#BackdropDropzone");

                    while (DropZone.firstChild) {
                        DropZone.removeChild(DropZone.firstChild);
                    }

                    let fileInput = document.createElement('input');
                    fileInput.type = 'file';

                    DropZone.appendChild(fileInput);

                    fileInput.onchange = () => {
                        let req = new XMLHttpRequest();
                        let formData = new FormData();

                        this.uploadInProccess = true;

                        formData.append("image", fileInput.files[0]);
                        req.open("POST", "/cover-process/backdrop");
                        req.send(formData);
                        req.onreadystatechange = () => {
                            if (req.readyState === 4) {
                                const response = JSON.parse(req.response);

                                this.uploadInProccess = false;
                                this.isVisible = true;

                                if (!response.success) {
                                    this.isErrors = true;
                                } else {
                                    this.isErrors = false;
                                    this.backdropImage = response.image
                                }

                                this.backdropData = response;
                            }
                        };
                    };
                    fileInput.click();
                },
            }
        })
    }

    function initSubtitlesUploader() {
        window['subtitlesVue'] = new Vue({
            el: '#subtitles-uploader',
            name: 'SubtitlesUploader',
            data() {
                return {
                    subtitlesLanguages: window['subtitles_languages'],
                    movie_meta: JSON.parse(window['movie_meta']),
                    subtitles: window['subtitles'],
                    uploadInProccess: false,
                    isSelectingLanguage: false,
                    isPlaying: false,
                    offset: 0,
                    language: '',
                    blobURL: '',
                    isVisible: false,
                    is_ajaxing: false,
                    can_admin: window['can_admin']
                }
            },
            computed: {
                approvedLanguages: function () {
                    let list = [];
                    this.subtitles.forEach(item => {
                        if (item.is_approved_default) {
                            list.push(item.language);
                        }
                    });
                    return list;
                }
            },
            filters: {
                MSS: function (value) {
                    return (value / 1000).toFixed(2);
                }
            },
            methods: {
                SaveSubtitle: function () {
                    $.get(this.blobURL, (contents) => {
                        this.handleSave(contents, window.subtitles_languages[this.language].language);
                    });
                },
                resetUploader: function () {
                    this.language = '';
                    this.isPlaying = false;
                    this.isVisible = false;
                },
                handleSave: function (contents, language) {
                    this.is_ajaxing = true;
                    let data = {
                        contents: contents,
                        language: language
                    };
                    $.ajax({
                        type: "POST",
                        url: '/moderation/movies/add-subtitle?id_movie=' + this.movie_meta.id_movie,
                        data: data,
                        success: (response) => {
                            this.is_ajaxing = false;
                            if (response.success) {
                                let isNew = true;
                                window.subtitles.forEach((item, index) => {
                                    if (item.language === response.data.language) {
                                        isNew = false;
                                        window.subtitles[index].id = response.data.id;
                                        window.subtitles[index].is_playing = false;
                                        window.subtitles[index].offset = this.offset;
                                        window.subtitles[index].is_approved = true;
                                        window.subtitles[index].is_approved_default = false;
                                        window.subtitles[index].is_deleted = false;
                                        window.subtitles[index].is_moderated = false;
                                        window.subtitles[index].url = response.data.url;
                                    }
                                });
                                if (isNew) {
                                    window.subtitles.push({
                                        id: response.data.id,
                                        url: response.data.url,
                                        language: response.data.language,
                                        is_approved: true,
                                        is_approved_default: false,
                                        is_moderated: false,
                                        is_editing: false,
                                        is_deleted: false,
                                        is_playing: false,
                                        offset: this.offset,
                                    });
                                }
                                this.resetUploader();
                            }
                        },
                        error: () => {
                            this.is_ajaxing = false;
                            alert('Something went wrong, please try again later');
                            this.subtitles_uploaded[idx].is_saving = false;
                        }
                    });
                },
                renderSaveDialog(idx) {
                    this.subtitles_uploaded[idx].is_saving = true;
                },
                DeleteSubtitle: function () {
                    this.isVisible = false;
                    this.url = '';
                    this.language = '';
                    if (this.isPlaying) {
                        this.isPlaying = false;
                        window.dispatchEvent(new Event('stopSubtitle'));
                    }
                },
                UploadClickHandle: function () {

                    let DropZone = document.querySelector("#SubtitlesDropzone");

                    while (DropZone.firstChild) {
                        DropZone.removeChild(DropZone.firstChild);
                    }

                    let fileInput = document.createElement('input');
                    fileInput.type = 'file';

                    DropZone.appendChild(fileInput);

                    fileInput.onchange = () => {
                        let req = new XMLHttpRequest();
                        let formData = new FormData();

                        this.uploadInProccess = true;

                        formData.append("srt", fileInput.files[0]);
                        req.open("POST", "/subtitles-process/");
                        req.send(formData);
                        req.onreadystatechange = () => {
                            if (req.readyState === 4) {
                                let blob = new Blob([req.response], {
                                    type: "text/vtt"
                                });
                                let blobURL = URL.createObjectURL(blob);
                                this.uploadInProccess = false;
                                this.blobURL = blobURL;
                                this.offset = 0;
                                this.isSelectingLanguage = true;
                            }
                        };
                    };
                    fileInput.click();
                },
                toggleSubtitlesPlay: function (idx) {

                    this.isPlaying = true;

                    window.dispatchEvent(new CustomEvent('playSubtitle', {
                        'detail': {
                            url: this.blobURL,
                            is_approved: false,
                            is_uploaded: true,
                            offset: this.offset
                        }
                    }));

                }
            },
            created() {
                window.addEventListener('playSubtitle', (e) => {
                    if (typeof (e.detail.is_uploaded) === 'undefined') {
                        this.isPlaying = false;
                    }
                });
                document.querySelector('#subtitles-uploader').classList.remove('faded');
            }
        });
    }


    function loadForm() {

        let formData = [];

        //quality_approved
        let quality_approved = $('#quality_approved');
        if (!!(+quality_approved.attr('data-default')) !== quality_approved.prop('checked')) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'quality_approved',
                    model: 'MoviesModeration',
                    value: quality_approved.prop('checked') ? '1' : '0'
                }
            });
        }

        // finalized subtitles
        let finalized_subs = $('#finalized_subs');
        if (!!(+finalized_subs.attr('data-default')) !== finalized_subs.prop('checked')) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'finalized_subs',
                    model: 'MoviesModeration',
                    value: finalized_subs.prop('checked') ? '1' : '0'
                }
            });
        }

        //is active
        let is_active = $('#is_active');
        if (!!(+is_active.attr('data-default')) !== is_active.prop('checked')) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'is_active',
                    model: 'Movies',
                    value: is_active.prop('checked') ? '1' : '0'
                }
            });
        }

        let title = $('#title');
        if (title.attr('default-value') !== title.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'title',
                    model: 'Movies',
                    value: title.val()
                }
            });
        }

        let year = $('#year');
        if (year.attr('default-value') !== year.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'year',
                    model: 'Movies',
                    value: year.val()
                }
            });
        }

        let description = $('#description');
        if (description.attr('default-value') !== description.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'description',
                    model: 'Movies',
                    value: description.val()
                }
            });
        }

        let homepage = $('#homepage');
        if (homepage.attr('default-value') !== homepage.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'homepage',
                    model: 'Movies',
                    value: homepage.val()
                }
            });
        }

        let country = $('#country');
        if (country.attr('default-value') !== country.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'country',
                    model: 'Movies',
                    value: country.val()
                }
            });
        }

        let duration = $('#duration');
        if (duration.attr('default-value') !== duration.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'duration',
                    model: 'Movies',
                    value: duration.val()
                }
            });
        }

        let youtube = $('#youtube');
        if (youtube.attr('default-value') !== youtube.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'youtube',
                    model: 'Movies',
                    value: youtube.val()
                }
            });
        }

        let imdb_id = $('#imdb_id');
        if (imdb_id.attr('default-value') !== imdb_id.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'imdb_id',
                    model: 'Movies',
                    value: imdb_id.val()
                }
            });
        }

        let tmdb_prefix = $('#tmdb_prefix');
        if (tmdb_prefix.attr('default-value') !== tmdb_prefix.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'tmdb_prefix',
                    model: 'Movies',
                    value: tmdb_prefix.val()
                }
            });
        }

        let budget = $('#budget');
        if (budget.attr('default-value') !== budget.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'budget',
                    model: 'Movies',
                    value: budget.val()
                }
            });
        }

        let flag_quality = $('#flag_quality');
        if (flag_quality.attr('default-value') !== flag_quality.val()) {
            formData.push({
                action: 'ModelUpdate',
                controller: 'Movies',
                data: {
                    id_movie: window['movie_meta_obj'].id_movie,
                    property: 'flag_quality',
                    model: 'Movies',
                    value: flag_quality.val()
                }
            });
        }

        window.dispatchEvent(new CustomEvent('collectedFormData', {
            'detail': formData
        }));

    }


    function postForm() {
        $('#draft-data').val(btoa(JSON.stringify(window['draft'])));
        setTimeout(function () {
            $('#draft-form').submit();
        }, 100);
    }

    function loadDraft() {

        let DraftLoaded = window.draft_loaded;

        DraftLoaded.forEach(item => {
            let data = JSON.parse(item.data);
            switch (item.action) {

                case 'ReconvertTorrentForce':
                    $('.reconvert-hidden').hide();
                    $('.reconvert-only').show();
                    break;

                case 'Reconvert':
                    $('.reconvert-hidden').hide();
                    $('.reconvert-only').show();
                    break;

                case 'DeleteSubtitle':
                    window.subtitles.forEach(item => {
                        if (item.id === data.id) {
                            item.is_deleted = true;
                        }
                    });
                    break;

                case 'UpdateSubtitle':
                    window.subtitles.forEach(item => {
                        if (item.id === data.id) {
                            item.offset = data.offset;
                        }
                    });
                    break;

                case 'AddSubtitle':
                    //No need anything to do, it will be picked automaticly
                    break;

                case 'ModelUpdate':
                    if (
                        data.property === 'quality_approved' ||
                        data.property === 'finalized_subs' ||
                        data.property === 'is_active') {
                        $('#' + data.property).prop('checked', +data.value);
                    } else {
                        $('#' + data.property).val(data.value);
                    }
                    break;

                case 'ApproveSubtitle':
                    window.subtitles.forEach(item => {
                        if (item.id === data.id) {
                            item.is_approved = data.is_approved === '1' ? true : false;
                        }
                    });
                    break;

                default:
                    alert('Unknown Action, Please Contact Site Admin.');
                    break;
            }
        });
    }

    /*
         * Load draft or delete all unused subtitles
         */
    if (draft_loaded.length === 0) {
        for (let i = 0; i < window.subtitles.length; i++) {
            if (window.subtitles[i].is_moderated === false) {
                $.get('/moderation/movies/subtitle-final-delete?id_subtitle=' + window.subtitles[i].id);
                window.subtitles.splice(i, 1);
                i--;
            }
        }
    } else {
        loadDraft();
    }

    window.videoJS = videojs("video_player", playerOptions);

    // update youtube URL
    $('#youtube').on('keyup', function () {
        $('#youtube + span a').attr('href', 'https://www.youtube.com/watch?v=' + $(this).val());
    });

    $('#youtube').on('change', function () {
        $('#youtube + span a').attr('href', 'https://www.youtube.com/watch?v=' + $(this).val());
    });

    initTorrentUploader();
    initSubtitlesUploader();
    initCoverUploader();
    initBackdropUploader();
    initNotApprovedList();
    initApprovedList();
    initPlayerController();

    let is_loadedForm = false;
    let is_loadedSubtitles = false;

    // Reset Subtitle Play in vueJS, when clicking play subtitles in uploaded one
    window.addEventListener('playSubtitle', (e) => {
        if (typeof (e.detail.is_uploaded) !== 'undefined' && e.detail.is_uploaded === true) {
            window.subtitles.forEach(item => {
                item.is_playing = false;
            });
        }
    });

    /*
     * Triggered by submit to approval / execute draft / save draft
     * Reads subtitles array changes
     */
    window.addEventListener('handleSubtitlesDraft', () => {
        let subtitles_draft = [];
        window.subtitles.forEach((item, index) => {

            if (item.is_deleted) {

                subtitles_draft.push({
                    action: 'DeleteSubtitle',
                    controller: 'Movies',
                    data: {
                        id: item.id
                    }
                });

            } else {

                if (item.is_moderated === false) {
                    subtitles_draft.push({
                        action: 'AddSubtitle',
                        controller: 'Movies',
                        data: {
                            id: item.id
                        }
                    });
                }

                if (item.offset !== 0) {
                    subtitles_draft.push({
                        action: 'UpdateSubtitle',
                        controller: 'Movies',
                        data: {
                            id: item.id,
                            offset: item.offset
                        }
                    });
                }

                if (this.subtitles[index].is_approved_default !== this.subtitles[index].is_approved) {
                    subtitles_draft.push({
                        action: 'ApproveSubtitle',
                        controller: 'Movies',
                        data: {
                            id: item.id,
                            is_approved: this.subtitles[index].is_approved ? '1' : '0'
                        }
                    });
                }
            }
        });
        window.dispatchEvent(new CustomEvent('collectedSubtitlesData', {
            'detail': subtitles_draft
        }));
    });

    /*
     * Tiggered after collected subtitles data from subtitles array
     *
     */
    window.addEventListener('collectedSubtitlesData', function (data) {
        is_loadedSubtitles = true;
        window.draft = window.draft.concat(data.detail);
        if (is_loadedSubtitles && is_loadedForm) {
            postForm();
        }
    });

    /*
     * Triggered before saving draft / submitting draft / executing draft
     */
    window.addEventListener('collectedFormData', function (data) {
        is_loadedForm = true;
        window.draft = window.draft.concat(data.detail);
        if (is_loadedSubtitles && is_loadedForm) {
            postForm();
        }
    });

    /*
     * Torrent Upload Event
     */
    $(document).on('click', 'a[href="#apply-torrent-for-movie"]', function (e) {
        e.preventDefault();
        window.dispatchEvent(new Event('handle-torrent-upload'));
    });

    $(document).on('click', '#apply-draft', function () {

        let postUrl = '/moderation/movies/execute-draft?id_draft=' + window.draft_id;
        $('#draft-form').attr('action', postUrl);

        is_loadedForm = false;
        is_loadedSubtitles = false;

        window.draft = [];

        // trigger data load
        loadForm();
        window.dispatchEvent(new Event('handleSubtitlesDraft'));

    });

    /*
     * Execute draft
     */
    $(document).on('click', '#submit-execute-draft', function () {

        $(this).attr('disabled', true);
        $('input[name=is_draft]').val('0');
        $('input[name=execute-now]').val('1');

        is_loadedForm = false;
        is_loadedSubtitles = false;

        window.draft = [];

        // trigger data load
        loadForm();
        window.dispatchEvent(new Event('handleSubtitlesDraft'));

    });

    /*
     * Save draft, so will be able to edit it later.
     */
    $(document).on('click', '#save-draft', function () {

        $(this).attr('disabled', true);

        is_loadedForm = false;
        is_loadedSubtitles = false;

        window.draft = [];

        // trigger data load
        loadForm();
        window.dispatchEvent(new Event('handleSubtitlesDraft'));

    });

    /*
     * Submit Draft To Approval
     */
    $(document).on('click', '#submit-draft', function () {

        $(this).attr('disabled', true);

        $('input[name=is_draft]').val('0');

        is_loadedForm = false;
        is_loadedSubtitles = false;

        window.draft = [];

        // trigger data load
        loadForm();
        window.dispatchEvent(new Event('handleSubtitlesDraft'));

    });
}
