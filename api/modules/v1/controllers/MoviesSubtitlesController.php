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
use api\modules\v1\models\site\MoviesSubtitles;

/**
 * Class MoviesController
 */
class MoviesSubtitlesController extends ActiveController
{

    public $modelClass = 'api\modules\v1\resources\MoviesSubtitles';

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
            ],
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

    public function actionAddSubtitles ($id_movie)
    {
        $params = \Yii::$app->request->post();

        $response = [
            'success' => false
        ];

        if (isset($params['subs'])) {
            try {
                $subs = json_decode($params['subs']);
                foreach ($subs as $subtitle) {
                    $model = MoviesSubtitles::find()->where(['id_movie' => $id_movie, 'language' => $subtitle->language])->one();

                    if ($model) {
                        $model->delete();
                    }

                    $model = new MoviesSubtitles();
                    $model->id_movie = $id_movie;
                    $model->url = $subtitle->path;
                    $model->language = $subtitle->language;
                    $model->shard = $subtitle->shard_url;
                    $model->is_approved = 1;
                    $model->is_moderated = 1;

                    $model->validate();
                    $model->save();
                }

                $response['success'] = true;
            } catch (Exception $e) {
                $response['message'] = $e->getMessage();   
            }
        }

        return $response;
    }

}
