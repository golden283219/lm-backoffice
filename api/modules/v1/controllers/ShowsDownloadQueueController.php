<?php

namespace api\modules\v1\controllers;

use backend\models\queue\Shows;
use backend\models\queue\ShowsMeta;
use common\models\site\ShowsEpisodes;
use console\jobs\downloadQueue\InsertMultiMagnet;
use console\jobs\downloadQueue\InsertSingleMagnet;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\Serializer;
use yii\web\Response;

use common\services\EpisodesQueueBuffer;

/**
 * Class MoviesController
 */
class ShowsDownloadQueueController extends ActiveController
{

    /**
     * @var \api\modules\v1\resources\queue\ShowsDownloadQueue
     */
    public $modelClass = 'api\modules\v1\resources\queue\ShowsDownloadQueue';

    public $serializer = [
        'class' => Serializer::class,
        'collectionEnvelope' => 'items'
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

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

    /**
     * Return Download Candidate With Given Status Code
     *
     * @param int $status status code used to grab download candidate
     *
     * @return array
     */
    public function actionQueryCandidate($status = 0)
    {
        $queueBuffer = new EpisodesQueueBuffer();

        return [
            'candidate' => $queueBuffer->getJob($status)
        ];
    }

    /**
     * Gets All Episodes From Download Queue
     *
     * @param $id_tvshow
     *
     * @return array
     */
    public function actionAllSeasonsEpisodes($id_tvshow)
    {
        $resp = [];

        $episodes = ShowsMeta::find()->where(['id_tvshow' => $id_tvshow])->orderBy(['season' => SORT_ASC, 'episode' => SORT_ASC])->asArray()->all();
        foreach ($episodes as $episode) {
            if (!isset($resp[$episode['season']])) {
                $resp[$episode['season']] = [];
            }

            $resp[$episode['season']][] = $episode;
        }

        return $resp;
    }


    /**
     * Insert episode with posted data
     */
    public function actionInsertEpisode()
    {
        $json = file_get_contents('php://input');
        $post_object = json_decode($json);

        $episode = ArrayHelper::getValue($post_object, 'episode', null);
        $priority = ArrayHelper::getValue($post_object, 'priority', 0);
        $id_tvshow = ArrayHelper::getValue($post_object, 'id_tvshow', 0);

        $model = ShowsMeta::find()
            ->where([
                'id_tvshow' => $id_tvshow,
                'episode' => $episode->episodeNumber,
                'season' => $episode->seasonNumber
            ])
            ->one();

        if ($model === null) {
            $model = new ShowsMeta();
        }

        $model->id_tvshow = $post_object->id_tvshow;
        $model->season = $episode->seasonNumber;
        $model->episode = $episode->episodeNumber;
        $model->air_date = implode('-', [$episode->air_date, '01', '01']);
        if ($model->isNewRecord) {
            $model->state = env('EPISODES_QUEUE_WAITING_TORRENT', '4');
        }
        $model->title = $episode->primary_title;
        $model->priority = $priority;

        if ($model->validate() && $model->save()) {
            return [
                'code' => 1,
                'message' => 'Succesfully added',
                'episode' => $model->getAttributes()
            ];
        }

        return [
            'code' => 0,
            'message' => 'Error adding episode',
            'errors' => $model->getErrors()
        ];
    }

    /**
     * Inserts tv show to download queue with posted data
     *
     * @return array
     */
    public function actionInsertShow()
    {
        $json = file_get_contents('php://input');
        $post_object = json_decode($json);

        $response = [
            'status' => Shows::STATUS_ERROR,
            'message' => 'Error adding tv show'
        ];

        $model = Shows::find()->where(['imdb_id' => $post_object->imdb_id])->one();

        if (!empty($model)) {
            $response['status'] = Shows::STATUS_EXISTS;
            $response['message'] = 'TV Show Already Exists';
            $response['show'] = $model;
            return $response;
        }

        $model = new Shows();
        $model->imdb_id = $post_object->imdb_id;
        $model->title = $post_object->title;
        $model->first_air_date = $post_object->first_air_date;
        $model->episode_duration = $post_object->episode_duration;
        $model->total_seasons = 0;
        $model->total_episodes = 0;
        $model->original_language = \common\helpers\Languages::get_iso_by_name($post_object->original_language);

        if ($model->validate() && $model->save()) {
            $response['status'] = Shows::STATUS_SUCCESS;
            $response['message'] = 'TV Shows Succesfully Saved';
            $response['show'] = $model;
            return $response;
        }

        $response['errors'] = $model->getErrors();

        return $response;
    }

    public function actionQueryCandidateById($id)
    {
        return [
            'candidate' => $this->modelClass::QueryCandidateById($id)
        ];
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

    public function actionSaveShowsToQueue()
    {
        $showInfo = file_get_contents('php://input');

        if (empty($showInfo)) {
            return [
                'success' => false,
                'message' => 'Request is Empty.',
                'status' => 2
            ];
        }

        parse_str($showInfo, $showInfo);

        if ($showInfo['type'] === 'single') {
            Yii::$app->downloadQueueTasks->push(
                new InsertSingleMagnet([
                    'showInfo' => $showInfo
                ])
            );

            return [
                'success' => true,
                'message' => 'Single Episode Added',
                'status' => 1
            ];
        }

        if ($showInfo['type'] === 'multi') {
            Yii::$app->downloadQueueTasks->push(
                new InsertMultiMagnet([
                    'showInfo' => $showInfo
                ])
            );

            return [
                'success' => true,
                'message' => 'Multi Episode Added',
                'status' => 1
            ];
        }

        return [
            'success' => false,
            'message' => 'Error adding episode',
            'status' => 2
        ];
    }

    /**
     * @deprecated
     * Update Queue
     */
    public function actionSaveShowsToQueueDeprecated()
    {
        $showInfo = file_get_contents('php://input');
        if (empty($showInfo)) {
            return [
                'success' => false,
                'message' => 'Unable to find requested show. Request is Empty.',
                'status' => 2
            ];
        }

        parse_str($showInfo, $showInfo);

        if (!empty($showInfo['tvmaze_id'])) {
            $modelShows = Shows::find()->where(['tvmaze_id' => $showInfo['tvmaze_id']])->one();
        }

        if (empty($modelShows) && !empty($showInfo['tvdb_id'])) {
            $modelShows = Shows::find()->where(['tvdb_id' => $showInfo['tvdb_id']])->one();
        }

        if (empty($modelShows) && !empty($showInfo['imdb_id'])) {
            $modelShows = Shows::find()->where(['imdb_id' => $showInfo['imdb_id']])->one();
        }

        if (empty($modelShows)) {
            return [
                'success' => false,
                'message' => 'Unable to find show in download queue.',
                'status' => 2
            ];
        }

        $showsMetaModel = ShowsMeta::find()
            ->where([
                'id_tvshow' => $modelShows->id_tvshow,
                'episode' => $showInfo['episode'],
                'season' => $showInfo['season'],
            ])
            ->one();

        /**
         * Skip if we don't find episode or it's being converted
         */
        if (empty($showsMetaModel) || $showsMetaModel->state == '3') {
            return [
                'success' => false,
                'message' => 'Unable to find episode in download queue.',
                'status' => 2
            ];
        }

        $siteEpisode = ShowsEpisodes::find()
            ->where([
                'id_shows' => $modelShows->id_tvshow,
                'episode' => $showInfo['episode'],
                'season' => $showInfo['season'],
            ])
            ->one();

        $flag_quality = !empty($siteEpisode) && !empty($siteEpisode->flag_quality) ? $siteEpisode->flag_quality : 0;

        // If we have episode on site and quality on site lower than we are trying to add
        if ($showsMetaModel->state == '1' && $flag_quality >= intval($showInfo['flag_quality'], 10)) {
            return [
                'success' => false,
                'message' => 'Already have episode on site with better or same quality.',
                'status' => 4
            ];
        }

        if ($showsMetaModel->state == '4' && !empty($showsMetaModel->torrent_blob) && $showsMetaModel->state == '1') {
            return [
                'success' => false,
                'message' => 'Episode already being reconverted.',
                'status' => 4
            ];
        }

        $response = [
            'success' => true,
            'message' => 'Episode updated',
            'status' => 1
        ];

        // set status for response - replaced
        if ($showsMetaModel->state == '1') {
            $response['status'] = 3;
        }

        if (empty($showInfo['state'])) {
            $showsMetaModel->state = env('EPISODES_QUEUE_WAITING_TORRENT', 4);
        }

        if (!empty($showInfo['rel_title'])) {
            $showsMetaModel->rel_title = $showInfo['rel_title'];
        }

        $showsMetaModel->type = 1;
        $showsMetaModel->torrent_blob = $showInfo['link'];
        $showsMetaModel->flag_quality = intval($showInfo['flag_quality'], 10);

        $showsMetaModel->priority = 99;

        if ($showsMetaModel->validate()) {
            $showsMetaModel->save();
        }

        return $response;
    }

}
