<?php

use backend\widgets\assets\EpisodeApplyTorrentAssets;

EpisodeApplyTorrentAssets::register($this);
?>

<div class="modal fade" id="modal-episode-apply-torrent" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Apply Torrent: <span id="modal-episode-apply-torrent--episode-number"></span></h4>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="episode-apply-torrent-content" rows="6"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                <div class="btn-group" style="display: flex; justify-content: flex-end;">
                    <input type="number" name="priority" value="101" id="priority" class="form-control" style="max-width: 150px; margin-right: 10px;">
                    <button type="button" class="btn btn-primary" id="apply-torrent-to-episode">Apply</button>
                </div>
            </div>
        </div>
    </div>
</div>
