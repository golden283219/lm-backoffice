<?php

namespace backend\modules\moderation\controllers;

use backend\modules\moderation\models\MoviesModeration;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\components\log\UsersActionLog;
use backend\models\site\MoviesReports;
use backend\models\site\Movies;
use common\components\movies\MoviesStorageComponent;


class MoviesReportsController extends Controller
{

    public $layout = '/common.php';
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
     * Lists all MoviesReports models.
     * @return mixed
     */
    public function actionIndex()
    {
        $query = MoviesModeration::find();

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->pagination->setPageSize(30);

        $dataProvider->query->andWhere(['or',
            ['locked_by' => null],
            ['locked_by' => \Yii::$app->user->identity->id]
        ]);

        $dataProvider->query->andWhere('active_reports_count > 0');

        $dataProvider->sort = [
            'defaultOrder' => [
                'locked_by' => SORT_DESC,
                'latest_reports_timestamp' => SORT_DESC
            ]
        ];

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    private function notifyUser($email)
    {

    }

    public function actionView($id)
    {
        $back_url = \Yii::$app->request->referrer ?: '/moderation/movies-reports';

        $reports = MoviesReports::find()->where(['id_movie' => $id, 'is_closed' => 0])->orderBy(['created_at' => SORT_DESC])->limit(25)->all();
        $movie = Movies::find()->where(['id_movie' => $id])->one();

        if ($movie->moviesModeration->locked_by === null || $movie->moviesModeration === \Yii::$app->user->identity->id) {
            $movie->moviesModeration->locked_by = \Yii::$app->user->identity->id;
            $movie->moviesModeration->is_locked = 2;

            $movie->moviesModeration->save();
        }

        return $this->render('view', [
            'reports' => $reports,
            'movie' => $movie,
            'movie_streams' => MoviesStorageComponent::get_video_streams($movie->id_movie),
            'movie_subtitles' => MoviesStorageComponent::get_movie_subtitles($movie->id_movie),
            'edge' => query_edge(),
            'back_url' => $back_url
        ]);
    }

    public function actionCloseAll($id_movie)
    {
        $back_url = '/moderation/movies-reports';

        MoviesReports::close_all_tickets_by_id_movie($id_movie);

        $model = MoviesModeration::find()->where(['id_movie' => $id_movie])->one();
        $model->active_reports_count = 0;
        $model->latest_reports_timestamp = 0;
        $model->is_locked = 0;
        $model->locked_by = null;


        // select active reports
        $active_reports = (int)MoviesReports::find()->where(['is_closed' => 0, 'id_movie' => $id_movie])->count();

        if ($active_reports > 0) {
            $latest_report = MoviesReports::find()->where(['is_closed' => 0, 'id_movie' => $id_movie])->orderBy(['created_at' => SORT_DESC])->one();

            $model->active_reports_count = $active_reports;
            $model->latest_reports_timestamp = $latest_report->created_at;
        }

        $model->validate();
        $model->save();

        \Yii::$app->getSession()->setFlash('success', "Movie #$id_movie - All Reports Successfully closed");

        return $this->redirect($back_url);
    }

}
