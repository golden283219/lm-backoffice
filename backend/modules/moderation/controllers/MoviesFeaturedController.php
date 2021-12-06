<?php

namespace backend\modules\moderation\controllers;

use Yii;
use common\helpers\StorageWorker;
use backend\models\site\Movies;
use backend\models\site\MoviesFeaturedSearch;
use backend\models\site\MoviesFeatured;
use backend\models\site\MoviesModeration;
use backend\models\site\ModerationDraft;
use backend\modules\moderation\controllers\DraftExecutors;
use backend\models\site\ModerationDraftItems;
use common\components\log\UsersActionLog;
use common\models\SubtitlesLanguages;
use common\models\site\MoviesSubtitles;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Exception;
use common\components\movies\MoviesStorageComponent;

/**
 * MoviesFeaturedController implements the CRUD actions for Movies model.
 */
class MoviesFeaturedController extends Controller
{

    public $UsersActionLogger;

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

    public function beforeAction($action)
    {
        $this->UsersActionLogger = new UsersActionLog();

        return parent::beforeAction($action);
    }

    /**
     * Lists all Movies models.
     * @return mixed
     */
    public function actionIndex()
    {
        // Remember Back URL for Create Movie Page
        Url::remember(['movies/index'], 'add-movie-back');

        $searchModel = new MoviesFeaturedSearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination->pageSize = 20;

        $dataProvider->sort = [
            'defaultOrder' => ['date_added' => SORT_DESC]
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentUrl' => \Yii::$app->homeUrl . \Yii::$app->request->url
        ]);
    }

    public function actionRedirect($id)
    {

        return $this->render('redirect', [
            'id' => $id
        ]);
    }

    /**
     * Displays a single MoviesFeatured model.
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
     * Creates a new MoviesFeatured model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MoviesFeatured();

        if ($model->load(Yii::$app->request->post())) {
            $model->date_added = date("Y-m-d H:i:s");

            if($model->save()){
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MoviesFeatured model.
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
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {

        $this->findModel($id)->delete();
        return $this->redirect(['index']);

    }

    public function actionSearch(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;

        $get = $request->get();

        $movies = Movies::find()->select(['id_movie', 'title', 'year'])->where([
            'like', 'title', $get['search']
        ]);

        return $movies->all();
    }

    public function actionSearchById(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;

        $get = $request->get();

        $movies = Movies::find()->select(['id_movie', 'title', 'year'])->where([
            'id_movie' => $get['id']
        ]);

        return $movies->one();
    }

    /**
     * Finds the MoviesFeatured model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MoviesFeatured the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MoviesFeatured::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
