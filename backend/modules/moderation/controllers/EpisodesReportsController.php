<?php

namespace backend\modules\moderation\controllers;

use common\models\ShowsReports;
use common\models\ShowsReportsSearch;
use Yii;
use common\models\site\ShowsEpisodesReportsCache;
use common\models\site\ShowsEpisodesReportsCacheSearch;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ShowsEpisodesReportsCacheController implements the CRUD actions for ShowsEpisodesReportsCache model.
 */
class EpisodesReportsController extends Controller
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
     * Lists all ShowsEpisodesReportsCache models.
     * @return mixed
     */
    public function actionIndex()
    {
        Url::remember();

        $searchModel = new ShowsEpisodesReportsCacheSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->sort = [
            'defaultOrder' => [
                'last_reported_at' => SORT_DESC
            ]
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing ShowsEpisodesReportsCache model.
     * If deletion is successful, the browser will be redirected to the remembered page.
     *
     * @param $id_episode
     *
     * @return mixed
     */
    public function actionCloseAll($id_episode)
    {
        ShowsEpisodesReportsCache::deleteAll('id_episode = ' . $id_episode);
        ShowsReports::deleteAll('id_episode = ' . $id_episode);

        return $this->redirect([Url::previous() ?? '/moderation/episodes-reports']);
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function actionCloseTicket($id)
    {
        $model = ShowsReports::find()->where(['id' => $id])->one();

        if (empty($model)) {
            throw new BadRequestHttpException('Something went wrong while closing report.');
        }

        $model->is_closed = 1;
        $model->id_user = \Yii::$app->user->identity->id;

        if (!$model->save()) {
            throw new BadRequestHttpException('Something went wrong while closing report.');
        }

        $count = ShowsReports::find()->where(['id_episode' => $model->id_episode, 'is_closed' => 0])->count();

        // Remove from cache if reports count is less than 0
        if ($count < 1) {
            ShowsEpisodesReportsCache::deleteAll('id_episode = ' . $model->id_episode);
        } else {
            ShowsEpisodesReportsCache::updateAll(['count' => $count], 'is_closed = 0 and id_episode = ' . $model->id_episode);
        }

        \Yii::$app->getSession()->setFlash('success', 'Report #' . $model->id . ' Successfully closed.');

        if ($count < 1) {
            return $this->redirect(['/moderation/episodes-reports']);
        }

        return $this->redirect([Url::previous()]);
    }

    /**
     * @param $id_episode
     *
     * @return string
     */
    public function actionView($id_episode)
    {
        $this->layout = '/common.php';
        Url::remember();

        $searchModel = new ShowsReportsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['is_closed' => 0]);
        $dataProvider->query->andWhere(['id_episode' => $id_episode]);

        $dataProvider->query->andWhere(['or',
            ['id_user' => null],
            ['id_user' => \Yii::$app->user->identity->id]
        ]);

        $dataProvider->sort = [
            'defaultOrder' => [
                'created_at' => SORT_DESC
            ]
        ];

        return $this->render('view', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the ShowsEpisodesReportsCache model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ShowsEpisodesReportsCache the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ShowsEpisodesReportsCache::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
