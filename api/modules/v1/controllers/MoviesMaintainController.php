<?php

namespace api\modules\v1\controllers;

use yii\rest\ActiveController;
use yii\rest\Serializer;
use api\modules\v1\helpers\MoviesStorageHelper;
use api\modules\v1\models\site\Movies;
use api\modules\v1\models\site\MoviesModeration;

class MoviesMaintainController extends ActiveController
{

  public $modelClass = 'api\modules\v1\resources\MoviesStorage';

  public $serializer = [
    'class' => Serializer::class,
    'collectionEnvelope' => 'items'
  ];


  public function actionAllMoviesStorage()
  {
    $model = new $this->modelClass();
    $items = $model->GetMoviesStorage();

    $response = [];

    foreach ($items as $item) {
      $response[$item['id_movie']][] = [
        'id_storage' => $item['id_storage'],
        'url' => str_ireplace('storage', 'stor', $item['shard_url']) . $item['url']
      ];
    }

    return $response;

  }

  public function actionUpdateMoviesStorage($action)
  {

    $data = $_POST;

    $helper = new MoviesStorageHelper();

    return $helper->$action($data);

  }

  public function actionInsertModerationStatus ($id_movie)
  {
    $oldMoviesModerationModel = MoviesModeration::find()->where(['id_movie' => $id_movie])->one();
    if ($oldMoviesModerationModel) {
      $oldMoviesModerationModel->delete();
    }

    $MoviesModerationModel = new MoviesModeration();
    $MoviesModerationModel->id_movie = $id_movie;

    if ($MoviesModerationModel->validate() && $MoviesModerationModel->save()) {
      return [
        'success' => true
      ];
    }

    return [
      'success' => false
    ];
    
  }

  public function actionGetAllV1 () {

    $resp = [];

    $movies = Movies::findAll(['new_converter' => 1]);

    foreach($movies as $movie) {
      $resp[] = [
        'id_movie' => $movie['id_movie'],
        'shard_url' => $movie['shard_url']
      ];
    }

    return $resp;
    
  }

}
