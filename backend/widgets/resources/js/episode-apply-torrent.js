window.addEventListener('TriggerApplyEpisodeTorrent', function (e) {
    var id_meta = e.detail.id_meta,
        episodeNumber = e.detail.episode,
        seasonNumber = e.detail.season,
        title = e.detail.title,
        year = e.detail.year.split('-');

    year = year.length === 3 ? year[0] : '0';
    window.__ApplyEpisodeTorrentID = id_meta;

    $('#episode-apply-torrent-content').val('');

    // init modal
    $('#modal-episode-apply-torrent--episode-number').html(title + '(' + year + ') ' +'S' + seasonNumber + 'E' + episodeNumber);
    $('#modal-episode-apply-torrent').modal('show');
});

$(document).on('click', '#apply-torrent-to-episode', function (e) {
    if (typeof (window.__ApplyEpisodeTorrentID) !== 'undefined') {
        $.post('/moderation/episodes-download-queue/apply-torrent', {
            _csrf: yii.getCsrfToken(),
            priority: $('#priority').val(),
            link: $('#episode-apply-torrent-content').val().trim(),
            id_meta: window.__ApplyEpisodeTorrentID
        }).then(function (response) {
            $.notify(response, 'success');
        }).catch(function (err) {
            $.notify('Error applying torrent. ' + err.responseText);
        });
    }

    $('#modal-episode-apply-torrent').modal('hide');
    window.dispatchEvent(new Event('AppliedTorrentToEpisode'));
});
