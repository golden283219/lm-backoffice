<?php

namespace backend\modules\moderation\controllers;

use Yii;
use backend\modules\moderation\models\MoviesModerationHistory;
use backend\modules\moderation\models\MoviesModerationHistorySearch;
use backend\models\queue\Movies;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MoviesModerationHistoryController implements the CRUD actions for MoviesModerationHistory model.
 */
class MoviesModerationHistoryController extends Controller
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
     * Lists all MoviesModerationHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MoviesModerationHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['id'=>SORT_DESC];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MoviesModerationHistory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MoviesModerationHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MoviesModerationHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MoviesModerationHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MoviesModerationHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Restarts History With Given $id
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionRestart($id)
    {

        $extract_torrent_data = function ($data) {
          if ($data === null) {
              throw new NotFoundHttpException('Unable to extract torrent data.');
          }

          $data_obj = json_decode($data);

          return [
              'rel_title' => isset($data_obj->torrentTitle) ? base64_decode($data_obj->torrentTitle) : '',
              'torrent_blob' => isset($data_obj->torrentBlob) ? base64_decode($data_obj->torrentBlob) : '',
          ];

        };

        if (($history = MoviesModerationHistory::findOne($id)) !== null && ($movie = Movies::findOne($history->id_movie)) !== null) {
            switch ($history->type) {
                case 0:
                    $torrent__history_data = $extract_torrent_data($history->data);

                    $movie->type = $history->type;
                    $movie->history_guid = $history->guid;
                    $movie->torrent_blob = $torrent__history_data['torrent_blob'];
                    $movie->rel_title = $torrent__history_data['rel_title'];
                    $movie->is_downloaded = env('MOVIES_QUEUE_TORRENT', 13);
                    break;
                case 1:
                    $torrent__history_data = $extract_torrent_data($history->data);

                    $movie->type = $history->type;
                    $movie->history_guid = $history->guid;
                    $movie->torrent_blob = $torrent__history_data['torrent_blob'];
                    $movie->rel_title = $torrent__history_data['rel_title'];
                    $movie->is_downloaded = env('MOVIES_QUEUE_TORRENT', 13);
                    break;

                case 2:
                    $movie->type = $history->type;
                    $movie->history_guid = $history->guid;
                    $movie->is_downloaded = env('MOVIES_QUEUE_USENET', 10);
                    break;
                default:
                    throw new NotFoundHttpException('Unknown movie type.');
            }

            if ($movie->save()) {
                Yii::$app->getSession()->setFlash('success', 'History Item Restarted.');
                return $this->redirect(Yii::$app->request->referrer);
            }

            Yii::$app->getSession()->setFlash('error', 'Unable to update movie status.');
            return $this->redirect(Yii::$app->request->referrer);

        }

        throw new NotFoundHttpException('The requested `$id` does not exist.');
    }

    /**
     * Finds the MoviesModerationHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MoviesModerationHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MoviesModerationHistory::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
