<?php

namespace backend\modules\moderation\controllers\DraftExecutors;

use common\controllers\MoviesController;
use backend\models\site\Movies;
use backend\modules\moderation\models\MoviesModeration;
use common\models\MoviesSubtitles;
use common\helpers\StorageWorker;
use backend\models\queue\Movies as MoviesQueue;
use common\models\site\Movies as MoviesSite;


class MoviesExecutor {

  public $Movies;
  public $MoviesModeration;
  public $MoviesSubtitles;

  public function __construct() {

    $this->Movies = new Movies();
    $this->MoviesModeration = new MoviesModeration();
    $this->MoviesSubtitles = new MoviesSubtitles();

  }

  public function Reconvert ($data) {

    $dataObj = json_decode($data);
    $MoviesController = new MoviesController();
    $MoviesController->actionReconvert($dataObj->id_movie, $dataObj->silent);
    return true;

  }

  public function ReconvertTorrentForce ($data) {

    $dataObj = json_decode($data);

    $MoviesModel = MoviesSite::findOne($dataObj->id_movie);
    $MoviesQueueModel = MoviesQueue::findOne($dataObj->id_movie);

    if (!$MoviesQueueModel || !$MoviesModel) {
      throw new Exception('Cant Find Requested Movie.', '404');
    }

    $MoviesQueueModel->is_downloaded = $dataObj->status_code;
    $MoviesQueueModel->type = $dataObj->type;
    $MoviesQueueModel->rel_title = $dataObj->rel_title;
    $MoviesQueueModel->flag_quality = 0;
    $MoviesQueueModel->torrent_blob = $dataObj->content;
    $MoviesQueueModel->priority = 6;

    if ($MoviesQueueModel->validate() && $MoviesQueueModel->save()) {
      return true;
    }

    throw new Exception('Can\'t set movie to reconvert. Unable to save \'MoviesQueueModels\'.');

  }

	public function ModelUpdate ($data) {

    $dataObj = json_decode($data);

    $model = $this->{$dataObj->model}::findOne(['id_movie' => $dataObj->id_movie]);

    if ($model) {
      $model->{$dataObj->property} = $dataObj->value;
      $model->validate();
      $model->save();
      return true;
    }

    return false;

  }


  public function ApproveSubtitle ($data) {

    $dataObj = json_decode($data);

    $model = $this->MoviesSubtitles::findOne(['id' => $dataObj->id]);
    $model->is_approved = $dataObj->is_approved;

    if ($model->validate() && $model->save()) {
      return true;
    }

    return false;

  }

  private function setModeration ($id, $status) {

    $model = MoviesSubtitles::findOne(['id' => $id]);
    $model->is_moderated = $status;

    if ($model->validate() && $model->save()) {
      return true;
    }

    return false;

  }

  public function UpdateSubtitle ($data) {

    $StorageWorker = new StorageWorker();
    $dataObj = json_decode($data);

    $subtitle = MoviesSubtitles::find()->where(['id' => $dataObj->id])->one();

    if ($subtitle) {
      $movie = Movies::find()->where(['id_movie' => $subtitle->id_movie])->one();
      $shard = str_ireplace('storage', 'stor', $movie->shard_url);
      $path_parts = explode('/', $subtitle->url);
      $StorageWorker->AddAction('offset', json_encode(['offset' => $dataObj->offset]), "{$shard}{$path_parts['0']}/{$path_parts['1']}/subtitles/{$path_parts['2']}.vtt");
    }
    
  }

  public function DeleteSubtitle ($data) {

    $StorageWorker = new StorageWorker();

    $dataObj = json_decode($data);

    $subtitle = MoviesSubtitles::find()->where(['id' => $dataObj->id])->one();

    if ($subtitle) {

      $movie = Movies::find()->where(['id_movie' => $subtitle->id_movie])->one();
    
      $shard = str_ireplace('storage', 'stor', $movie->shard_url);
      $path_parts = explode('/', $subtitle->url);
      
      $StorageWorker->AddAction('delete', json_encode([]), "{$shard}{$path_parts['0']}/{$path_parts['1']}/subtitles/{$path_parts['2']}.vtt");

      $subtitle->delete();

    }
    
  }

  public function AddSubtitle ($data) {

    $StorageWorker = new StorageWorker();
    $dataObj = json_decode($data);

    $subtitle = MoviesSubtitles::find()->where(['id' => $dataObj->id])->one();

    if ($subtitle) {
      
      $movie = Movies::find()->where(['id_movie' => $subtitle->id_movie])->one();
      $shard = str_ireplace('storage', 'stor', $movie->shard_url);
      $path_parts = explode('/', $subtitle->url);

      $this->setModeration($dataObj->id, 1);

      $new_file_name = str_ireplace('_mod', '', $path_parts['2']);

      $StorageWorker->AddAction('move', json_encode(['new_path' => "{$shard}{$path_parts['0']}/{$path_parts['1']}/subtitles/{$new_file_name}.vtt"]), "{$shard}{$path_parts['0']}/{$path_parts['1']}/subtitles/{$path_parts['2']}.vtt");

      $subtitle->url = "{$path_parts['0']}/{$path_parts['1']}/$new_file_name";

      $subtitle->validate();
      $subtitle->save();

    }

  }

}
