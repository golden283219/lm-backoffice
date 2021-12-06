<?php

namespace api\modules\v1\controllers;

use api\modules\v1\resources\Article;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\rest\IndexAction;
use yii\rest\OptionsAction;
use yii\rest\Serializer;
use yii\rest\ViewAction;
use yii\web\HttpException;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Class MoviesController
 */
class GarbageCollectionController extends ActiveController
{

  public $modelClass = 'api\modules\v1\resources\GarbageCollection';

  public $serializer = [
    'class' => Serializer::class,
    'collectionEnvelope' => 'items'
  ];

  public function behaviors()
  {

    $behaviors = parent::behaviors();

    $behaviors['contentNegotiator'] = [
      'class' => ContentNegotiator::class,
      'formatParam' => 'o',
      'formats' => [
        'application/json' => Response::FORMAT_JSON,
        'application/xml' => Response::FORMAT_XML,
      ]
    ];

    return $behaviors;

  }

  public function actions() 
  { 

    $actions = parent::actions();
    $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
    return $actions;
    
  }

  /**
    * @return ActiveDataProvider
    */
  public function prepareDataProvider()
  {

    $data = $_GET;

    $filter = [];

    if (isset($_GET['filter']) && is_array($_GET['filter'])) {
      $filter = $_GET['filter'];
    }

    return new ActiveDataProvider(array(
      'query' => $this->modelClass::find()->where($filter)
    ));

  }

  public function actionUpdateMovies($action)
  {

    $data = $_POST;

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

}
