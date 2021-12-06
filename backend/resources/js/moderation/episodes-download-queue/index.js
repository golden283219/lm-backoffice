function bindDeleteGlobalEvents() {
    $(document).on('click', '[data-ajax-method="delete"]', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/moderation/episodes-download-queue/delete?id=' + $(e.target).attr('data-id'),
            success: function () {
                $.pjax.reload('#grid-shows-1-pjax');
            },
        });
    });
}

function initStatusSelector() {
    let selected = extractParameterFromUrlByName('ShowsMetaSearch[state]');

    $('[name="ShowsMetaSearch[state]"]').hide().parent().append('<select id="ShowsMetaState"></select>');

    var optEmpty = new Option('', '', selected === '');
    var optFinished = new Option('Finished', '1', false, selected === '1');
    var optBeingConverted = new Option('Being Converted', '3', false, selected === '3');
    var optNoCandidate = new Option('No Candidate', 'no-candidate', false, selected === 'no-candidate');
    var optWaitingConverter = new Option('Waiting(Conversion Script)', 'waiting', false, selected === 'waiting');

    var showsMetaStateEl = $('#ShowsMetaState');
    showsMetaStateEl.select2({
        multiple: false
    }).on('change', function (e) {
        e.stopPropagation();

        $('[name="ShowsMetaSearch[state]"]')
            .val($('#ShowsMetaState').val())
            .trigger('change');
    });

    showsMetaStateEl
        .append(optEmpty)
        .append(optWaitingConverter)
        .append(optFinished)
        .append(optBeingConverted)
        .append(optNoCandidate);
}

function initSearchIdByTitle() {
    $('[name="ShowsMetaSearch[id_tvshow]"]').hide().parent().append('<select id="ShowsMetaIdTvhow"></select>');

    $('#ShowsMetaIdTvhow').select2({
        placeholder: typeof (window.showTitle) !== 'undefined' ? window.showTitle + ' (' + window.showYear + ')' : '',
        ajax: {
            url: yii2app.apiBaseURL + '/shows/search-queue',
            dataType: 'json',
            processResults: function (data) {
                let results = [];
                results.push({
                    id: '',
                    text: '[ANY TV SHOW]'
                });
                data.forEach(item => {
                    results.push({
                        id: item.id_tvshow,
                        text: item.title + ' (' + item.year.split('-')['0'] + ')'
                    });
                });
                return {
                    results: results
                };
            }
        }
    }).on('change', function (event) {
        event.stopPropagation();
        $('input[name="ShowsMetaSearch[id_tvshow]"]')
            .val($('#ShowsMetaIdTvhow').val())
            .trigger('change');
    });
}

function bindBulkMagnetApplyEvents() {
    $(document).on('click', '#bulk-submit-magnet-link', function () {
        let magnet = $('textarea[name="bulk-apply-magnet-input"]').val();

        if (isValidaMagnetLink(magnet)) {
            axios.post('/moderation/episodes-download-queue/apply-torrent-bulk', {
                ids: getCheckboxValues(),
                priority: $('#input-priority').val(),
                magnetLink: magnet,
                _csrf: yii2app.csrf
            }).then(function (response) {
                $('#myModal').modal('toggle');
                $('textarea[name="bulk-apply-magnet-input"]').val('');
                $.pjax.reload('#grid-shows-1-pjax', {timeout: false});
                $.notify(response.data.message, {
                    className: response.data.success ? 'success' : 'error',
                    arrowSize: 22,
                    autoHideDelay: 8000
                });
            }).catch(function (error) {
                $('#myModal').modal('toggle');
                $.pjax.reload('#grid-shows-1-pjax', {timeout: false});
                $('textarea[name="bulk-apply-magnet-input"]').val('');
                alert(error);
            });
        } else {
            alert('Please Enter Valid Magnet Link');
        }
    });
}

$(document).on('click', '[data-bulk-action="1"]', function () {
    let action = $(this).attr('data-action'),
        ids = getCheckboxValues();
    if (action === '') {
        alert('Please Select Bulk Action');
        return;
    }

    if (ids.length === 0) {
        alert('Please Select Rows');
        return;
    }

    if (action === 'apply-magnet') {
        $('#myModal').modal('toggle');

        return;
    }

    runBulkAction('/moderation/episodes-download-queue/' + action, {
        ids: ids,
        _csrf: yii.getCsrfToken()
    }, function (response) {
        $('#apply-bulk-actions').button('loading');

        if (response.success) {
            $.notify(response.message, 'success');
            $.pjax.reload('#grid-shows-1-pjax', {timeout: false});
        } else {
            $.notify(response.message, 'error');
        }
    });
});

$(document).on('click', '.add-magnet-link', function (e) {
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

window.addEventListener('AppliedTorrentToEpisode', function () {
    $.pjax.reload('#grid-shows-1-pjax', {timeout: false});
});

$(document).on('pjax:success', function () {
    initSearchIdByTitle();
    initStatusSelector();
});

window.addEventListener('DOMContentLoaded', function () {
    initSearchIdByTitle();
    initStatusSelector();
    bindDeleteGlobalEvents();
    bindBulkMagnetApplyEvents();
});
