<?php

namespace api\modules\v1\controllers;

use backend\models\queue\Movies;
use common\models\MoviesModerationHistory;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\rest\Serializer;
use yii\web\Response;

/**
 * Class MoviesController
 */
class MoviesDownloadQueueController extends ActiveController
{

    public $modelClass = 'api\modules\v1\resources\MoviesDownloadQueue';

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

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    public function actionAddMovie()
    {
        $postData = Yii::$app->request->post();

        $sanitized_imdb_id = str_ireplace('tt', '', $postData['imdb_id']);

        $movie = Movies::find()->where(['imdb_id' => $sanitized_imdb_id])->one();

        if (!empty($movie) && ($movie->is_downloaded == 1 || $movie->is_downloaded == 3 || ($movie->is_downloaded == 13 && !empty($movie->torrent_blob)))) {
            return [
                'status' => 0
            ];
        }

        if (empty($movie)) {
            $movie = new Movies();
        }

        $movie->title = $postData['title'];
        $movie->year = $postData['year'];
        $movie->imdb_id = $sanitized_imdb_id;
        $movie->url = 'https://www.imdb.com/title/tt' . $sanitized_imdb_id;
        $movie->is_downloaded = (int)env('MOVIES_QUEUE_TORRENT');
        $movie->worker_ip = null;
        $movie->priority = 1;
        $movie->flag_quality = 0;
        $movie->source = 'T';
        $movie->torrent_blob = $postData['magnetLink'];
        $movie->rel_title = $postData['relTitle'];
        $movie->type = 1;
        $movie->original_language = $postData['original_language'];

        if ($movie->validate() && $movie->save()) {
            return [
                'status' => 1
            ];
        }

        return [
            'status' => 2
        ];
    }

    /**
     * Returns Torrent File For Given Movie
     * @param $id
     *
     * @return bool|string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionTorrent($id)
    {
        $DownloadQueueItem = $this->modelClass::find()->where(['id' => $id])->one();

        if (!isset($DownloadQueueItem) || !isset($DownloadQueueItem->torrent_blob) || $DownloadQueueItem->type === $this->modelClass::TYPE_MAGNET) {
            throw new \yii\web\NotFoundHttpException;
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        \Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename=' . $DownloadQueueItem->rel_title);
        \Yii::$app->response->headers->add('Content-Type', 'application/x-bittorrent');

        return base64_decode(trim(str_ireplace('data:application/octet-stream;base64,', '', $DownloadQueueItem->torrent_blob)));
    }

    private function update_history_status($res)
    {
        if (!empty($res) && !empty($res['history_guid'])) {
            $history_item = MoviesModerationHistory::find()->where(['guid' => $res['history_guid']])->one();

            if ($history_item !== null) {
                $history_item->status = $res['is_downloaded'];
                $history_item->worker_ip = $res['worker_ip'];
                $history_item->save();
            }
        }
    }

    public function actionGetCandidate($status = 10)
    {
        $candidate = [];

        $res = $this->modelClass::get_candidate($status);

        if ($res !== null) {
            $candidate = $res;
        }

        $this->update_history_status($res);

        return $candidate;
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
}
