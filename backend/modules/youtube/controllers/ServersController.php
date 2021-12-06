<?php

namespace backend\modules\youtube\controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use common\services\ShowsWorker;
use Yii;
use backend\models\queue\YtConverters;
use backend\models\queue\YtConvertersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\traits\FormAjaxValidationTrait;

/**
 * ServersController implements the CRUD actions for YtConverters model.
 */
class ServersController extends Controller
{

    use FormAjaxValidationTrait;

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
     * @return string
     * @throws \yii\base\ExitException
     */
    public function actionIndex()
    {
        $model = new YtConverters();
        $searchModel = new YtConvertersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'New Server Have Been Added.');
            return $this->redirect(['index']);
        }

        $dataProvider->sort = ['defaultOrder' => ['type' => SORT_ASC]];
        $dataProvider->pagination->pageSize = 0;

        return $this->render('index', [
            'model'        => $model,
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single YtConverters model.
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionCreate()
    {
        $model = new YtConverters();

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'New Server Have Been Added.');

            return $this->redirect(['index']);
        }
        return $this->renderPartial('create', [
            'model' => $model,
        ]);
    }

    public function actionBulkRestart()
    {
        $_promises = [];

        $client = new Client([
            'timeout'  => 5.0
        ]);

        $post = Yii::$app->request->post();

        $yt_converters = YtConverters::find()->where(['id' => $post['ids']])->asArray()->all();

        foreach ($yt_converters as $yt_converter) {
            $_promises[] = $client->getAsync($yt_converter['ip'] . '/process/action/restart');
        }

        Promise\settle($_promises)->wait();

        return 'Bulk Restart: Successful';
    }

    public function actionBulkDelete()
    {
        $post = Yii::$app->request->post();

        $condition = 'id =' . implode(' OR id = ', $post['ids']);

        YtConverters::deleteAll($condition);

        return 'Bulk Delete Successful';
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\base\ExitException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            \Yii::$app->getSession()->setFlash('success', 'Server Have Been Updated.');

            return $this->redirect(['index']);
        }
        return $this->renderPartial('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing YtConverters model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function actionDetails()
    {
        $response_contents = '<div class="alert alert-danger">Error Fetching Data</div>';

        $expandRowKey = Yii::$app->request->post('expandRowKey', null);

        if ($expandRowKey) {
            $fe_server_model = YtConverters::find()->where(['id' => $expandRowKey])->one();

            if (! $fe_server_model) {
                return $response_contents;
            }

            $shows_worker = new ShowsWorker($fe_server_model->ip);

            $response = ShowsWorker::wrap_promises([
                'system_health' => $shows_worker->async_get_health(),
                'process_list'  => $shows_worker->async_get_process_list()
            ]);

            foreach ($response['process_list'] as $key => $value) {
                if (isset($value->data)) {
                    $response['process_list'][$key]->data = json_decode($value->data);
                }
            }

            $response['process_list'] = array_filter($response['process_list'], function ($item) {
                return $item->status !== 1;
            });

            $_template_name = $fe_server_model->type === 0 ? '_shows_server_details' : '_movies_server_details';

            return $this->renderPartial($_template_name, [
                'system_health' => $response['system_health'],
                'process_list'  => $response['process_list']
            ]);
        }

        return $response_contents;
    }

    /**
     * Finds the YtConverters model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return YtConverters the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = YtConverters::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
