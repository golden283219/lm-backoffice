<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\queue\GarbageCollection;
use common\models\site\MoviesStorage;
use console\jobs\metadata\MoviesMetaQueue;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\rest\Serializer;
use yii\web\Response;

/**
 * Class MoviesController
 */
class MoviesController extends ActiveController
{

    public $modelClass = 'api\modules\v1\resources\Movies';

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

    public function actionAddOrUpdate($id_movie)
    {
        $params = Yii::$app->request->post();

        $movies_model = \api\modules\v1\models\site\Movies::find()->where(['id_movie' => $id_movie])->one();

        if (isset($movies_model)) {
            // put in garbage collector
            $GC = new GarbageCollection();
            $GC->storage = $movies_model->shard_url;
            $GC->path = 'movies/' . $movies_model->storage_slug;
            if (!$GC->validate() || !$GC->save()) {
                Yii::warning([
                    'message' => 'Unable to PUT in GarbageCollection',
                    'storage' => $GC->storage,
                    'path' => $GC->path
                ]);
            }

            // Delete Movies Storage Items
            foreach ($movies_model->moviesStorage as $storageItem) {
                $storageItem->delete();
            }

            //Delete Movies Subtitles
            foreach ($movies_model->moviesSubtitles as $movieSubtitle) {
                $movieSubtitle->delete();
            }

            // Delete Movies Audio Items
            foreach ($movies_model->moviesAudio as $movieAudio) {
                $movieAudio->delete();
            }
        } else {
            $movies_model = new \api\modules\v1\models\site\Movies;
        }

        $fields = $movies_model->attributes();

        if (YII_ENV_DEV) {
            Yii::info([
                'fields' => $fields,
                'params' => $params
            ]);
        }

        foreach ($fields as $field) {
            if (isset($params[$field])) {
                $movies_model[$field] = $params[$field];
            }
        }

        $movies_model->flag_quality = $this->detect_flag_quality($params);

        //Reset metadata and subtitles status, we need to redownload it
        $movies_model->has_metadata = 0;
        $movies_model->has_subtitles = 0;
        $movies_model->is_active = 0;

        if (!$movies_model->validate() || !$movies_model->save()) {
            Yii::warning([
                'message' => 'Unable to Update Movie Metadata.',
                'fields' => $params,
                'errors' => $movies_model->errors
            ]);

            return false;
        }

        $job = new MoviesMetaQueue([
            'imdbId' => $movies_model->imdb_id,
        ]);

        Yii::$app->metadataDownloadQueue->push($job);

        $this->insertStorageData($movies_model->id_movie, $params);
        $this->insertAudioData($movies_model->id_movie, $params);

        return $movies_model;
    }

    protected function detect_flag_quality($params)
    {
        if (isset($params['is1080p']) && $params['is1080p'] === '1') {
            return 8;
        }

        if (isset($params['is720p']) && $params['is720p'] === '1') {
            return 7;
        }

        return 6;
    }

    protected function insertStorageData($id_movie, $params)
    {
        $dict = [
            '1080p' => 1,
            '720p' => 0,
            '480p' => 4,
            '360p' => 3,
        ];

        $qualities = [];

        if (isset($params['is1080p']) && $params['is1080p'] === '1') $qualities[] = '1080p';
        if (isset($params['is720p']) && $params['is720p'] === '1') $qualities[] = '720p';
        if (isset($params['is480p']) && $params['is480p'] === '1') $qualities[] = '480p';
        if (isset($params['is360p']) && $params['is360p'] === '1') $qualities[] = '360p';

        foreach ($qualities as $quality) {
            $model = new \api\modules\v1\models\site\MoviesStorage;
            $model->quality = $dict[$quality];
            $model->is_converted = 1;
            $model->id_movie = $id_movie;
            $model->url = 'movies/' . $params['storage_slug'] . '/' . $quality . '/index.m3u8';
            if (!$model->validate() || !$model->save()) {
                Yii::warning([
                    'message' => 'Unable to Update MoviesStorage Metadata.',
                    'fields' => $params,
                    'errors' => $model->errors
                ]);
            }
        }
    }

    protected function insertAudioData($id_movie, $params)
    {
        $streams = [
            'dd-audio' => '0',
            'dd-audio-ac3' => '1'
        ];

        if (isset($params['is_dd']) && $params['is_dd'] === '1') {
            foreach ($streams as $key => $type) {
                $model = new \api\modules\v1\models\site\MoviesAudio;
                $model->shard = str_ireplace('/', '', $params['shard_url']);
                $model->id_movie = $id_movie;
                $model->storage_path = 'movies/' . $params['storage_slug'] . "/$key/" . 'audio.m3u8';
                $model->lang_iso_code = $params['original_language'];
                $model->type = $type;
                if (!$model->validate() || !$model->save()) {
                    Yii::warning([
                        'message' => 'Unable to Update MoviesAudio Metadata.',
                        'fields' => $params,
                        'errors' => $model->errors
                    ]);
                }
            }
        }
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

    /**
     * Gets Candidate
     *
     * @return array
     */
    public function actionQueryGcastCandidate()
    {
        $candidate = $this->modelClass::getGcastCandidate();

        if (isset($candidate) && count($candidate) > 0) {
            $candidate['storage_items'] = MoviesStorage::find()->where(['id_movie' => $candidate['id_movie']])->asArray()->all();
        }

        return $candidate;
    }
}
