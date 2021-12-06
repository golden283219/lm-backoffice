window.draft = [];

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

function loadForm() {

    let formData = [];

    //quality_approved
    let quality_approved = $('#quality_approved');
    if (!!(+quality_approved.attr('data-default')) !== quality_approved.prop('checked')) {
        formData.push({
            action: 'ModelUpdate',
            controller: 'Episodes',
            data: {
                id_episode: episode.id,
                property: 'quality_approved',
                model: 'Episode',
                value: quality_approved.prop('checked') ? '1' : '0'
            }
        });
    }

    // finalized subtitles
    let finalized_subs = $('#finalized_subs');
    if (!!(+finalized_subs.attr('data-default')) !== finalized_subs.prop('checked')) {
        formData.push({
            action: 'ModelUpdate',
            controller: 'Episodes',
            data: {
                id_episode: episode.id,
                property: 'finalized_subs',
                model: 'Episode',
                value: finalized_subs.prop('checked') ? '1' : '0'
            }
        });
    }

    //is active
    let is_active = $('#is_active');
    if (!!(+is_active.attr('data-default')) !== is_active.prop('checked')) {
        formData.push({
            action: 'ModelUpdate',
            controller: 'Episodes',
            data: {
                id_episode: episode.id,
                property: 'is_active',
                model: 'Episode',
                value: is_active.prop('checked') ? '1' : '0'
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

/*
 * Start Document initialization, once page is ready
 */
window.addEventListener('DOMContentLoaded', function () {

    $(document).on('click', '#apply-torrent-for-show', function (e) {
        e.preventDefault();
        window.dispatchEvent(
            new CustomEvent(
                'TriggerApplyEpisodeTorrent',
                {
                    detail: {
                        id_meta: e.target.dataset.id,
                        season: e.target.dataset.season,
                        episode: e.target.dataset.episode,
                        year: e.target.dataset.year,
                        title: e.target.dataset.title
                    }
                }
            )
        );
    });

    window.videoJS = videojs("video_player", playerOptions);

    let is_loadedForm = false;
    let is_loadedSubtitles = false;

    window.addEventListener('collectedSubtitlesData', function (data) {

        is_loadedSubtitles = true;
        window['draft'] = window['draft'].concat(data.detail);
        if (is_loadedSubtitles && is_loadedForm) {

        }

    });

    window.addEventListener('collectedFormData', function (data) {
        is_loadedForm = true;
        window['draft'] = window['draft'].concat(data.detail);
        if (is_loadedForm) {
            postForm();
        }
    });

    $(document).on('click', '#submit-execute-draft', function () {

        $(this).attr('disabled', true);

        $('input[name=is_draft]').val('0');
        $('input[name=execute-now]').val('1');

        is_loadedForm = false;
        is_loadedSubtitles = false;

        window['draft'] = [];

        // trigger data load
        loadForm();
        window.dispatchEvent(new Event('handleSubtitlesDraft'));

    });
});
