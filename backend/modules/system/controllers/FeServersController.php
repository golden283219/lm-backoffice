<?php

namespace backend\modules\system\controllers;

use Yii;
use backend\modules\system\models\FeServers;
use backend\modules\system\models\FeServersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\traits\FormAjaxValidationTrait;
use yii\filters\VerbFilter;

/**
 * FeServersController implements the CRUD actions for FeServers model.
 */
class FeServersController extends Controller
{

    use FormAjaxValidationTrait;

    public $layout = '/common.php';

    private static $disabledCsrfActions = [
        'bulk-delete',
        'bulk-enable',
        'bulk-disable'
    ];

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
        if (in_array($action->id, $this::$disabledCsrfActions)) {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionBulkDelete()
    {
        $post = Yii::$app->request->post();

        $condition = 'id =' . implode(' OR id = ', $post['ids']);

        FeServers::deleteAll($condition);

        return 'Bulk Delete Successful';
    }

    public function actionBulkEnable()
    {
        $post = Yii::$app->request->post();

        $condition = 'id =' . implode(' OR id = ', $post['ids']);

        FeServers::updateAll(['is_enabled' => 1], $condition);

        return 'Bulk Enable Successful';
    }

    public function actionBulkDisable()
    {
        $post = Yii::$app->request->post();

        $condition = 'id =' . implode(' OR id = ', $post['ids']);

        FeServers::updateAll(['is_enabled' => 0], $condition);

        return 'Bulk Disable Successful';
    }

    /**
     * Lists all FeServers models.
     * @return string|\yii\web\Response
     * @throws \yii\base\ExitException
     */
    public function actionIndex()
    {
        $model = new FeServers();
        $searchModel = new FeServersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $this->performAjaxValidation($model);

        $post_fields = Yii::$app->request->post();

        if ($model->load($post_fields)) {

            $streaming_servers_map = [];

            foreach (FeServers::domains as $domain) {
                if (isset($post_fields['FeServers']) && isset($post_fields['FeServers'][$domain]) && $post_fields['FeServers'][$domain] !== '') {
                    $streaming_servers_map[$domain] = $post_fields['FeServers'][$domain];
                }
            }

            $model->domain_mapped = $streaming_servers_map;

            if ($model->save()) {
                \Yii::$app->getSession()->setFlash('success', 'New Server Have Been Added.');
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('index', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionDumpServers () {
        $servers = FeServers::find()->where(['is_enabled' => '1'])->asArray()->all();

        $servers_formatted = array_map(function ($item) {
            return [
                'ip' => $item['ip'],
                'server_name' => $item['server_name'],
                'status' => $item['status_check_url'],
                'domain_mapped' => isset($item['domain_mapped']) ? $item['domain_mapped'] : [],
                'maxBW' => $item['max_bw']
            ];
        }, $servers);

        backup_config();

        if (file_put_contents(env('LB_CONFIG_PATH'), \GuzzleHttp\json_encode($servers_formatted)) && lb_api_do_config_update()) {
            \Yii::$app->getSession()->setFlash('success', "All Servers Have Been Dumped");
        } else {
            restore_config_backup();
            \Yii::$app->getSession()->setFlash('error', 'Something Went Wrong while Dumping. Backup config have been restored');
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing FeServers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $post_fields = Yii::$app->request->post();

        if ($model->load($post_fields)) {
            $streaming_servers_map = [];

            foreach (FeServers::domains as $domain) {
                if (isset($post_fields['FeServers']) && isset($post_fields['FeServers'][$domain]) && $post_fields['FeServers'][$domain] !== '') {
                    $streaming_servers_map[$domain] = $post_fields['FeServers'][$domain];
                }
            }

            $model->domain_mapped = $streaming_servers_map;

            if ($model->save()) {
                \Yii::$app->getSession()->setFlash('success', "Server #$id updated.");

                return $this->redirect(['index']);
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing FeServers model.
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
     * Finds the FeServers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FeServers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FeServers::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
