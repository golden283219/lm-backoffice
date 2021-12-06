<?php

namespace backend\modules\moderation\controllers;

use backend\models\queue\ShowsMeta;
use Http\Client\Common\Exception\HttpClientNotFoundException;
use Yii;
use backend\models\queue\Shows;
use common\models\queue\ShowsSearch;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ShowsDownloadQueueController implements the CRUD actions for Shows model.
 */
class ShowsDownloadQueueController extends Controller
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
     * Lists all Shows models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '/common.php';

        $searchModel = new ShowsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->orderBy(['date_added' => SORT_DESC]);

        Url::remember('', 'return_url');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Shows model.
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
     * Creates a new Shows model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new Shows();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_tvshow]);
        }
        return $this->render('add', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Shows model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_tvshow]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Get TV Show Details
     */
    public function actionDetail()
    {
        $expandRowKey = Yii::$app->request->post('expandRowKey', null);

        if ($expandRowKey) {
            $model = ShowsMeta::find()->where(['id_tvshow' => $expandRowKey])->asArray()->all();

            if (! $model) {
                return '<div class="alert alert-danger">No data found</div>';
            }

            return $this->renderPartial('_show_details', [
                'models' => $model
            ]);
        }

        return '<div class="alert alert-danger">No data found</div>';
    }

    public function actionEpisodes()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $postBody = \Yii::$app->getRequest()->getBodyParams();
        $model = ShowsMeta::find()->where(['id_tvshow' => $postBody['idTvShow']])->asArray()->all();

        $treeView = [];
        if (!empty($model)) {
            foreach ($model as $episode) {
                $nodeText = 'Season ' . $episode['season'];
                $seasonIndex = null;
                foreach ($treeView as $key => $node) {
                    if (!empty($node['text']) && $node['text'] == $nodeText) {
                        $seasonIndex = $key;
                        break;
                    }
                }

                $episode['text'] = $episode['title'];
                $state = !empty(ShowsMeta::$stateTypes[$episode['state']]) ? ShowsMeta::$stateTypes[$episode['state']] : '';
                $episodeData = [
                    'text' => "<div class='text_block'>
                                    <div class='name'>" . $episode['episode'] . " - " . $episode['title'] . "</div>
                                    <div class='status'>" .  $state . "</div>
                                    <div>" . $episode['air_date'] . "</div>
                               </div>",
                    'id_meta' => $episode['id_meta'],
                    'state' => [
                        'checked' => false,
                    ],
                ];
                if (isset($seasonIndex)) {
                    $treeView[$seasonIndex]['nodes'][] = $episodeData;
                } else {
                    $treeView[] = [
                        'text' => $nodeText,
                        'nodes' => [$episodeData],
                    ];
                }
            }
        }
        return $treeView;
    }

    public function actionUpdateEpisodes()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $postBody = \Yii::$app->getRequest()->getBodyParams();
        ShowsMeta::updateAll(['torrent_blob' => $postBody['torrent_blob']], ['in', 'id_meta', $postBody['ids_meta']]);
        //BaseVarDumper::dump($treeView, 100, true);
        return true;
    }

    /**
     * Apply magnet to tvshow form
     *
     * @param $id
     * @return string
     */
    public function actionApplyMagnet($id)
    {
        $this->layout = '/common';

        $show = Shows::find()->where(['id_tvshow' => $id])->one();

        if (empty($show)) {
            return new HttpClientNotFoundException();
        }

        return $this->render('apply-magnet', [
            'show' => $show
        ]);
    }

    /**
     * Finds the Shows model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Shows the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Shows::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
