<?php

use backend\assets\ShowApplyTorrentAsset;
use common\helpers\Html;

$year = date('Y', strtotime($show->first_air_date));

$this->title = 'Apply Magnet to "' . $show->title . ' (' . $year . ')"';

ShowApplyTorrentAsset::register($this);

?>

<script>
    window.id_tvshow = <?= $show->id_tvshow; ?>;
</script>

<div class="cc" id="episodes-torrent-uploader">
    <div class="step1" v-if="step === 1">
        <div class="form-group">
            <label>Magnet Link</label>
            <textarea class="form-control" name="content" rows="6" placeholder="Enter ..." v-model="magnet"></textarea>
        </div>
        <div class="form-group" style="text-align: right; display: flex; align-content: flex-end; justify-content: flex-end;">
            <?= Html::input('number', 'priority', 101, [
                'v-model' => 'priority',
                'class' => 'form-control',
                'style' => 'max-width: 150px; margin-right: 10px',
                'id'    => 'priority'
            ]); ?>
            <button v-on:click="getTorrentMetadata()" class="btn btn-primary" :disabled="isDisabledStep1 || isFetchingTorrentData">
                <span v-if="isFetchingTorrentData">Loading...</span>
                <span v-else>Next</span>
            </button>
        </div>
    </div>
    <div class="step2" v-if="step === 2">
        <DraggableTree :data="EpisodesTreeData" draggable cross-tree>
            <div slot-scope="{data, store, vm}">
                <template>
                    <div @click="store.toggleOpen(data)">
                      <b v-if="data.children && data.children.length">{{data.open ? '-' : '+'}}&nbsp;</b>
                      <span class="episode-title">{{data.title}}</span>
                      <span class="badge" v-if="typeof(data.state) !== 'undefined'" v-bind:class="{'badge-danger': data.state == '5', 'badge-success': data.state == '1', 'badge-dark': data.state == '3','badge-info': data.state == '4'}">
                        <i>{{data.stateText}}</i>
                      </span>
                    </div>
                </template>
            </div>
        </DraggableTree>
        <DraggableTree :data="FilesTreeData" draggable cross-tree>
            <div slot-scope="{data, store, vm}">
                <template>
                    <div @click="store.toggleOpen(data)">
                        <b v-if="data.children && data.children.length">{{data.open ? '-' : '+'}}&nbsp;</b>
                        <i class="fa" v-bind:class="data.icon"></i> {{data.title}}
                    </div>
                </template>
            </div>
        </DraggableTree>
        <div class="bottom-controls">
          <button v-on:click="reconvertMultipleEpisodeMap(); isUpdatingTorrentMap = true" class="btn btn-primary" :disabled="isUpdatingTorrentMap" style="display: block;">
              <span v-if="isUpdatingTorrentMap">Saving...</span>
              <span v-else>Save</span>
          </button>
        </div>
    </div>
</div>
