<?php

namespace backend\modules\moderation\controllers\DraftExecutors;

use common\controllers\EpisodesController;
use common\models\ShowsEpisodes;
use backend\models\queue\ShowsMeta;
use common\models\ShowsEpisodesSubtitles;
use common\helpers\StorageWorker;

class EpisodesExecutor {

  public $Episodes;
  public $EpisodesSubtitles;

  public function __construct() {

    $this->Episode = new ShowsEpisodes();
    $this->EpisodesSubtitles = new ShowsEpisodesSubtitles();

  }

  public function Reconvert ($data) {
    $dataObj = json_decode($data);
    $EpisodesController = new EpisodesController();
    $EpisodesController->actionReconvert($dataObj->id_episode, $dataObj->priority, $dataObj->silent);
    return true;
  }
  
  public function ReconvertTorrentForce ($data) {

    $dataObj = json_decode($data);

    $SiteEpisode = ShowsEpisodes::find()
    ->where(['id' => $dataObj->id_episode])
    ->one();

    if ($SiteEpisode) {
      
      $episode_meta = ShowsMeta::find()
      ->where([
        'id_tvshow' => $SiteEpisode->id_shows,
        'episode' => $SiteEpisode->episode,
        'season' => $SiteEpisode->season
      ])
      ->one();
      
      if ($episode_meta) {
        $episode_meta->state = env('EPISODES_QUEUE_WAITING_TORRENT');
        $episode_meta->type = $dataObj->type;
        $episode_meta->rel_title = $dataObj->rel_title;
        $episode_meta->priority = 3;
        $episode_meta->torrent_blob = $dataObj->content;
        
        if ($episode_meta->validate() && $episode_meta->save()) {
          return true;
        }
      }
      
    }

    throw new Exception('Can\'t set episode to reconvert with torrent. Unable to save \'ShowsQueueModels\'.');

  }

  public function ModelUpdate ($data) {

    $dataObj = json_decode($data);

    $model = $this->{$dataObj->model}::findOne(['id' => $dataObj->id_episode]);

    if ($model) {
      
      $model->{$dataObj->property} = $dataObj->value;
      
      if ($model->validate() && $model->save()) {
        
        return true;
      
      }
      
    }

    return false;

  }

  public function ApproveSubtitle ($data) {

    $dataObj = json_decode($data);

    $model = $this->EpisodesSubtitles::findOne(['id' => $dataObj->id]);
    $model->is_approved = $dataObj->is_approved;

    if ($model->validate() && $model->save()) {
      return true;
    }

    return false;

  }

  private function setModeration ($id, $status) {

    $model = ShowsEpisodesSubtitles::findOne(['id' => $id]);
    $model->is_moderated = $status;

    if ($model->validate() && $model->save()) {
      return true;
    }

    return false;

  }

  public function UpdateSubtitle ($data) {

    $StorageWorker = new StorageWorker();
    $dataObj = json_decode($data);

    $subtitle = ShowsEpisodesSubtitles::find()->where(['id' => $dataObj->id])->one();

    if ($subtitle) {
      $shard = str_ireplace('storage', 'stor', $subtitle->shard);
      $file_name = $subtitle->is_moderated === 1 ? $subtitle->isoCode . '.vtt' : $subtitle->isoCode . '_mod.vtt';
      $StorageWorker->AddAction('offset', json_encode(['offset' => $dataObj->offset]), "/{$shard}/{$subtitle->storagePath}$file_name");
    }
    
  }

  public function DeleteSubtitle ($data) {

    $StorageWorker = new StorageWorker();
    $dataObj = json_decode($data);

    $subtitle = ShowsEpisodesSubtitles::find()->where(['id' => $dataObj->id])->one();

    if ($subtitle) {

      $shard = str_ireplace('storage', 'stor', $subtitle->shard);

      $file_name = $subtitle->is_moderated === 1 ? $subtitle->isoCode . '.vtt' : $subtitle->isoCode . '_mod.vtt';
      
      $StorageWorker->AddAction('delete', json_encode([]), "/{$shard}/{$subtitle->storagePath}$file_name");

      $subtitle->delete();

    }
    
  }

  public function AddSubtitle ($data) {

    $StorageWorker = new StorageWorker();
    $dataObj = json_decode($data);

    $subtitle = ShowsEpisodesSubtitles::find()->where(['id' => $dataObj->id])->one();

    if ($subtitle) {

      $shard = str_ireplace('storage', 'stor', $subtitle->shard);

      $this->setModeration($dataObj->id, 1);

      $new_file_name = $subtitle->isoCode.'.vtt';
      $old_file_name = $subtitle->isoCode . '_mod.vtt';

      $StorageWorker->AddAction('move', json_encode(['new_path' => "/{$shard}/{$subtitle->storagePath}$new_file_name"]), "/{$shard}/{$subtitle->storagePath}$old_file_name");

      $subtitle->validate();
      $subtitle->save();

    }

  }
  
  private function putInGarbageCollection($storage, $path)
  {

    $episode_path = $this->sanitizeEpisodePath($path);

    if ($episode_path) {
      $gc = new GarbageCollection();
      $gc->storage = $storage;
      $gc->path = $episode_path;
      if ($gc->save()) {
        return true;
      }
    }

    return false;

  }

}