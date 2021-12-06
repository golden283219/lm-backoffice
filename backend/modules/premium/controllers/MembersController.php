<?php

namespace backend\modules\premium\controllers;

use backend\models\PremPaymentsHistory;
use common\models\PremPlans;
use common\models\site\PremSignupForm;
use common\traits\FormAjaxValidationTrait;
use Yii;
use backend\models\PremUsers;
use backend\models\PremUsersSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsersController implements the CRUD actions for PremUsers model.
 */
class MembersController extends Controller
{
    use FormAjaxValidationTrait;

    public $layout = '/common.php';

    private static $no_csrf_actions = ['add-time', 'sub-time', 'delete-payment', 'add-payment-history'];

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
        if (in_array($action->id, self::$no_csrf_actions)) {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * Lists all PremUsers models.
     *
     * @return mixed
     * @throws \yii\base\ExitException
     */
    public function actionIndex()
    {
        $searchModel = new PremUsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->isPost) {
            $model = new PremSignupForm();

            $this->performAjaxValidation($model);

            if ($model->load(Yii::$app->request->post()) &&  $model->validate()) {
                $model->signup();
                return $this->refresh();
            }
        }

        $dataProvider->sort = [
            'defaultOrder' => [
                'latest_transaction_date' => SORT_DESC,
                'created_at'              => SORT_DESC
            ]
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws \yii\base\ExitException
     */
    public function actionAddPaymentHistory()
    {
        Yii::$app->response->format = Yii::$app->response::FORMAT_JSON;

        $model = new PremPaymentsHistory();
        $post = Yii::$app->request->post();

        $post['PremPaymentsHistory']['paid_at'] = strtotime($post['PremPaymentsHistory']['paid_at']);
        $post['PremPaymentsHistory']['created_at'] = $post['PremPaymentsHistory']['paid_at'];

        $package = PremPlans::find()->where(['id' => $post['PremPaymentsHistory']['id_prem_plan']])->one();
        $post['PremPaymentsHistory']['title'] = $package->title;
        $post['PremPaymentsHistory']['paid_usd'] = $package->month_count * $package->price_usd;

        if ($model->load($post) && $model->save()) {
            return [
                'success' => true,
                'fields' => [
                    'id_prem_user' => $post['PremPaymentsHistory']['id_prem_user'],
                ],
                'errors' => []
            ];
        }

        return [
            'success' => 'false',
            'errors' => $model->errors
        ];

    }

    public function actionDeletePayment($id)
    {
        Yii::$app->response->format = Yii::$app->response::FORMAT_JSON;

        $payment_id = Yii::$app->request->post('payment_id');

        if (empty($payment_id)) {
            return false;
        }

        return PremPaymentsHistory::deleteAll("id_prem_user = $id AND id = $payment_id");
    }

    /**
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
     * Creates a new PremUsers model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws \yii\base\ExitException
     */
    public function actionCreate()
    {
        $model = new PremUsers();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PremUsers model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
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
     * @return string
     */
    public function actionDetail()
    {
        $expandRowKey = Yii::$app->request->post('expandRowKey', null);

        if ($expandRowKey) {
            $model = PremUsers::findOne($expandRowKey);
            $payments = PremPaymentsHistory::find()->where(['id_prem_user' => $model->id])->all();

            if (! $model) {
                return '<div class="alert alert-danger">No data found</div>';
            }

            return $this->renderPartial('_member_details', [
                'model'    => $model,
                'payments' => $payments
            ]);
        }

        return '<div class="alert alert-danger">No data found</div>';
    }

    /**
     * @param $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionAddTime($id)
    {
        Yii::$app->response->format = Yii::$app->response::FORMAT_JSON;

        $member = self::findModel($id);

        $seconds = Yii::$app->request->post('duration', 0);

        $member->cancel_timestamp = max($member->cancel_timestamp, time()) + (int)$seconds;

        if ($member->save()) {
            return [
                'status' => 'OK',
                'message' => 'Success'
            ];
        }

        throw new BadRequestHttpException('Unable to save user model');
    }

    /**
     * @param $id
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function actionSubTime($id)
    {
        Yii::$app->response->format = Yii::$app->response::FORMAT_JSON;

        $member = self::findModel($id);

        $seconds = Yii::$app->request->post('duration', 0);

        $member->cancel_timestamp = max($member->cancel_timestamp - (int)$seconds, 0);

        if ($member->save()) {
            return [
                'status' => 'OK',
                'message' => 'Success'
            ];
        }

        throw new BadRequestHttpException('Unable to save user model');
    }

    /**
     * Deletes an existing PremUsers model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     *
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PremUsers model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PremUsers the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PremUsers::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
