<?php

namespace api\modules\v1\controllers;

use backend\models\site\MoviesStorage;
use api\modules\v1\resources\Article;
use Yii;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use yii\rest\IndexAction;
use yii\rest\OptionsAction;
use yii\rest\Serializer;
use yii\rest\ViewAction;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Class MoviesController
 */
class MoviesStorageController extends ActiveController
{

    public $modelClass = 'api\modules\v1\resources\MoviesStorage';

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

    /**
     * @return bool|string
     * @throws BadRequestHttpException
     * @throws Yii\db\Exception
     * @throws \Throwable
     */
    public function actionUpdateUrl()
    {
        $post = json_decode(file_get_contents('php://input'));

        if (empty($post) || empty($post->id_movies_storage) || empty($post->path)) {
            throw new BadRequestHttpException("Received wrong POST, while /v1/moviesMoviesStorage->actionUpdateUrl\n".json_encode($post, JSON_PRETTY_PRINT), 500);
        }

        $model = MoviesStorage::find()->where(['id' => $post->id_movies_storage])->one();

        if (empty($model)) {
            throw new BadRequestHttpException("Unable to find model, while /v1/moviesMoviesStorage->actionUpdateUrl\n".json_encode($post, JSON_PRETTY_PRINT), 500);
        }

        if (empty($post->path)) {
            $model->delete();

            return 'OK';
        }

        $model->url = MoviesStorage::extractURLFromStoragePath($post->path);
        $model->is_converted = 10;

        if (!$model->validate() || !$model->save()) {
            throw new \yii\db\Exception('Unable to save model,' , json_encode([
                'modelErrors' => $model->errors,
                'post'        => $post
            ], JSON_PRETTY_PRINT), 500);
        }

        return 'OK';
    }
}
