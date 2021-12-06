<?php

namespace common\controllers;

use \backend\models\queue\Movies as MoviesQueue;
use backend\models\site\Movies as MoviesSite;
use yii\db\Exception;

class MoviesController {

  public function actionReconvert ($id_movie, $is_silent) {

    $MoviesQueueModel = MoviesQueue::findOne($id_movie);
    $MoviesModel = MoviesSite::findOne($id_movie);

    if (!$MoviesQueueModel || !$MoviesModel) {
      throw new Exception('Cant Find Requested Movie.', '404');
    }

    $bad_titles = unserialize($MoviesQueueModel->bad_titles);
    $bad_titles[] = md5($MoviesModel->rel_title);

    $MoviesQueueModel->bad_titles = serialize($bad_titles);
    $MoviesQueueModel->bad_guids = serialize([]);
    $MoviesQueueModel->is_downloaded = env('MOVIES_QUEUE_USENET');
    $MoviesQueueModel->flag_quality = 0;
    $MoviesQueueModel->priority = 3;

    if ((bool)$is_silent === false) {

      $MoviesModel->is_active = MoviesSite::STATUS_INACTIVE;
      $MoviesModel->validate();
      $MoviesModel->save();

    }

    if ($MoviesQueueModel->validate() && $MoviesQueueModel->save()) {
      return true;
    }

    throw new Exception('Can\'t set movie to reconvert. Unable to save \'MoviesQueueModels\'.');

  }

}