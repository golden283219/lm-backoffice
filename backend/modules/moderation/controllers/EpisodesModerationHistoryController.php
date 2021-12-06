<?php

namespace backend\modules\moderation\controllers;

use backend\models\PremPaymentsHistory;
use backend\models\PremUsers;
use Yii;
use backend\modules\moderation\models\EpisodesModerationHistory;
use backend\modules\moderation\models\EpisodesModerationHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EpisodesModerationHistoryController implements the CRUD actions for EpisodesModerationHistory model.
 */
class EpisodesModerationHistoryController extends Controller
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
     * Lists all EpisodesModerationHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EpisodesModerationHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];
        $dataProvider->query->addGroupBy(['imdb_id']);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionDetail()
    {
        $expandRowKey = Yii::$app->request->post('expandRowKey', null);

        if ($expandRowKey) {
            $model = EpisodesModerationHistory::findOne($expandRowKey);

            if (! $model) {
                return '<div class="alert alert-danger">No data found</div>';
            }

            return $this->renderPartial('_item_details', [
                'model'    => $model
            ]);
        }

        return '<div class="alert alert-danger">No data found</div>';
    }

    /**
     * Displays a single EpisodesModerationHistory model.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EpisodesModerationHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EpisodesModerationHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EpisodesModerationHistory model.
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
     * Deletes an existing EpisodesModerationHistory model.
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
     * Finds the EpisodesModerationHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EpisodesModerationHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EpisodesModerationHistory::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
