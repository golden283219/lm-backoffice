<?php

namespace backend\modules\moderation\controllers;

use Yii;
use backend\modules\moderation\models\MoviesDownloadQueueSearch;
use yii\helpers\Url;
use yii\web\Controller;
use \backend\models\queue\Movies as MoviesQueue;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MoviesDownloadQueueController implements the CRUD actions for MoviesDownloadQueue model.
 */
class MoviesDownloadQueueController extends Controller
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
     * Lists all MoviesDownloadQueue models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // Remember Back URL for Create Movie Page
        Url::remember(['movies-download-queue/index'], 'add-movie-back');

        $searchModel = new MoviesDownloadQueueSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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

    public function actionCreate()
    {
        $errors = [];

        $model = new MoviesQueue();

        if (Yii::$app->request->post()) {

            $post = Yii::$app->request->post();

            if (!isset($post['IMDbID']) || $post['IMDbID'] === '') {
                $errors['IMDbID'] = 'IMDbID can not be empty';
            }

            if (!isset($post['title']) || $post['title'] === '') {
                $errors['title'] = 'Title can not be empty';
            }

            if (!isset($post['year']) || $post['year'] === '') {
                $errors['year'] = 'Year can not be empty';
            }

            if (!isset($post['original_language']) || $post['original_language'] === '') {
                $errors['original_language'] = 'OriginalLanguage can not be empty';
            }

            if (count($errors) === 0) {

                $imdb_id = str_ireplace('tt', '', $post['IMDbID']);

                $model = MoviesQueue::find()->where(['imdb_id' => $imdb_id])->one();
                if (null === $model) {
                    $model = new MoviesQueue();
                }

                $model->title = $post['title'];
                $model->year = $post['year'];
                $model->imdb_id = $imdb_id;
                $model->url = 'https://www.imdb.com/title/tt' . $imdb_id;
                $model->is_downloaded = (int)env('MOVIES_QUEUE_USENET');
                $model->worker_ip = null;
                $model->priority = 1;
                $model->flag_quality = 0;
                $model->source = 'M';

                if (isset($post['release-title']) && $post['release-title'] !== '' && isset($post['content']) && $post['content'] !== '') {
                    $model->is_downloaded = (int)env('MOVIES_QUEUE_TORRENT');
                    $model->torrent_blob = $post['content'];
                    $model->rel_title = $post['release-title'];
                    $model->type = isMagnetLink($post['content']) ? 1 : 0;
                }

                if ($model->validate() && $model->save()) {
                    Yii::$app->getSession()->setFlash('success', "Movie: {$model->title} ({$model->year}) added to queue.");
                    return $this->redirect(Url::previous('add-movie-back') ?: ['/moderation/movies-download-queue']);
                }
            }

        }


        return $this->render('create', [
            'model' => $model,
            'errors' => $errors
        ]);

    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $post = Yii::$app->request->post();

            if (!isset($post['Movies']['rel_title']) || !isset($post['Movies']['torrent_blob']) || $post['Movies']['torrent_blob'] === '' || $post['Movies']['rel_title'] === '') {
                $model->rel_title = null;
                $model->torrent_blob = null;
            } else {
                $model->type = isMagnetLink($model->torrent_blob) ? 1 : 0;
            }

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MoviesQueue::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
