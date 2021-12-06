<?php

namespace backend\modules\moderation\controllers;

use common\helpers\TorrentsRegistryHelper;
use Exception;
use Yii;
use backend\models\queue\ShowsMeta;
use backend\models\queue\ShowsMetaSearch;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * ShowsDownloadQueueController implements the CRUD actions for ShowsMeta model.
 */
class EpisodesDownloadQueueController extends Controller
{

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all ShowsMeta models.
     *
     * @return mixed
     * @throws Exception
     */
    public function actionIndex()
    {
        $this->layout = '/common';
        $searchModel = new ShowsMetaSearch();

        $queryParams = Yii::$app->request->queryParams;

        $state = ArrayHelper::getValue($queryParams, 'ShowsMetaSearch.state');
        if ($state === 'waiting') {
            $queryParams['ShowsMetaSearch']['state'] = '0;4';
        } else if ($state === 'no-candidate') {
            $queryParams['ShowsMetaSearch']['state'] = '0;4;5';
            $queryParams['ShowsMetaSearch']['show_with_torrents'] = false;
        }

        $dataProvider = $searchModel->search($queryParams);

        Url::remember('', 'redirect_url');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->type = 1;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $url = Url::previous('episodes_download_queue');
            \Yii::$app->getSession()->setFlash('success', "Episode #{$model->id_meta} updated");

            return $this->redirect([isset($url) ? $url : '/moderation/episodes-download-queue']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Set To Reconvert Given Episode ID
     * @param $id
     * @return \yii\web\Response
     */
    public function actionReconvert($id)
    {
        $url = Url::previous('episodes_download_queue');

        return $this->redirect([isset($url) ? $url : '/moderation/episodes-download-queue']);
    }

    public function actionApplyTorrentBulk()
    {
        Yii::$app->response->format = Yii::$app->response::FORMAT_JSON;

        $post = Yii::$app->request->post();

        $ids = ArrayHelper::getValue($post, 'ids');
        $magnetLink = ArrayHelper::getValue($post, 'magnetLink');
        $priority = ArrayHelper::getValue($post, 'priority', 99);

        if (!is_array($ids) || empty($ids) || empty($magnetLink)) {
            return [
                'success' => false,
                'message' => 'No Episodes Ids Supplied'
            ];
        }

        $torrentRegistry = new TorrentsRegistryHelper(['priority' => $priority]);

        // Insert and update torrent map
        foreach ($ids as $id_meta) {
            $torrentRegistry->insert($magnetLink, $id_meta);
        }

        return [
            'success' => true,
            'message' => 'Updated "' . count($ids) . ' episodes" with magnet link'
        ];
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function actionApplyTorrentBulkWithMap()
    {
        Yii::$app->response->format = Yii::$app->response::FORMAT_JSON;

        $post = Yii::$app->request->getBodyParams();

        $priority = ArrayHelper::getValue($post, 'priority', 99);

        $torrentRegistry = new TorrentsRegistryHelper(['priority' => $priority]);

        $cnt = 0;
        foreach ($post['episodesMap'] as $episode) {
            $torrentRegistry->insert($post['magnet'], $episode['id_meta'], $episode['torrentPath']);
            $cnt++;
        }

        return [
            'success' => true,
            'count'   => $cnt,
            'return_url' => Url::previous('redirect_url') ?? '/moderation/shows-download-queue',
        ];
    }

    /**
     * @return string
     */
    public function actionApplyTorrent()
    {
        $post = Yii::$app->request->post();

        $episode = ShowsMeta::find()
            ->where(['id_meta' => $post['id_meta']])
            ->one();

        if (empty($episode) || !$episode) {
            Yii::$app->response->statusCode = 404;
            return 'Requested Episode Not Found.';
        }

        $episode->worker_ip = null;
        $episode->torrent_blob = $post['link'];
        $episode->state = env('EPISODES_QUEUE_WAITING_TORRENT');
        $episode->rel_title = extractMagnetDN($post['link']);
        $episode->priority = ArrayHelper::getValue($post, 'priority', 99);
        $episode->type = 1;

        if ($episode->validate() && $episode->save()) {
            Yii::$app->response->statusCode = 200;
            return 'Torrent Successfully Applied';
        }

        Yii::$app->response->statusCode = 500;
        return 'Unable to save episode. ' . json_encode($episode->errors, JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->post('ids');

        ShowsMeta::deleteAll(['id_meta' => $ids]);

        return 'Deleted...';
    }

    /**
     * Delete Episode Download Queue Item By ID
     *
     * @param $id
     *
     * @return string
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return '';
    }

    /**
     * Find's Model
     *
     * @param $id
     *
     * @return ShowsMeta|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = ShowsMeta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
