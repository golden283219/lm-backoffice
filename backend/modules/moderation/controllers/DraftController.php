<?php

namespace backend\modules\moderation\controllers;

use Yii;
use backend\modules\moderation\models\ModerationDraft;
use common\models\ShowsEpisodes;
use common\components\log\UsersActionLog;
use backend\modules\moderation\models\DraftSearch;
use backend\models\site\MoviesModeration;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\Exception;
use yii\filters\VerbFilter;
use backend\modules\moderation\controllers\DraftExecutors;

/**
 * DraftController implements the CRUD actions for ModerationDraft model.
 */
class DraftController extends Controller
{

    public $layout = '/common.php';
    public $UsersActionLogger;

    //Executors
    public $Movies;
    public $Episodes;

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
        $this->Movies = new DraftExecutors\MoviesExecutor();
        $this->Episodes = new DraftExecutors\EpisodesExecutor();
        return parent::beforeAction($action);

    }

    /**
     * Lists all ModerationDraft models.
     * @return mixed
     */
    public function actionIndex()
    {

        $searchModel = new DraftSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere('status = 0');
        $dataProvider->query->andWhere('is_active = 1');

        $dataProvider->sort = [
            'defaultOrder' => ['created_at' => SORT_DESC]
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {

        $model = $this->findModel($id);

        if ($model->category === ModerationDraft::CATEGORY_MOVIES) {
            return $this->redirect(['/moderation/movies/update?id=' . $model->id_media . '&back_url=' . urlencode('/moderation/draft') . '&draft_id=' . $model->id]);
        }

        if ($model->category === ModerationDraft::CATEGORY_TVEPISODES) {
            return $this->redirect(['/moderation/episodes/update?id=' . $model->id_media . '&back_url=' . urlencode('/moderation/draft') . '&draft_id=' . $model->id]);
        }

    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionExecute($id)
    {

        $draft = $this->findModel($id);

        foreach ($draft->draftItems as $draftItem) {
            $this->{$draftItem->controller}->{$draftItem->action}($draftItem->data);
        }

        $draft->status = ModerationDraft::STATUS_EXECUTED;
        $draft->executed_by = \Yii::$app->user->identity->id;

        if ($draft->validate() && $draft->save()) {

            if ((int)$draft->category === ModerationDraft::CATEGORY_MOVIES) {
                $model = MoviesModeration::find()->where(['id_movie' => $draft->id_media])->one();
                $model->is_locked = 0;
                $model->locked_at = null;
                $model->locked_by = null;
                $model->validate();
                $model->save();
            } else if ((int)$draft->category === ModerationDraft::CATEGORY_TVSHOWS) {

            } else if ((int)$draft->category === ModerationDraft::CATEGORY_TVEPISODES) {
                $ShowsEpisodes = ShowsEpisodes::find()->where(['id' => $draft->id_media])->one();
                if ($ShowsEpisodes) {
                    $ShowsEpisodes->is_locked = 0;
                    $ShowsEpisodes->locked_at = null;
                    $ShowsEpisodes->locked_by = null;
                    $ShowsEpisodes->validate();
                    $ShowsEpisodes->save();
                }
            } else {
                // something went wrong;
            }

            $this->UsersActionLogger->logAction('ExecuteDraft', 'Drafts', json_encode([
                'draft_id' => $draft->id,
                'draft_url' => '/moderation/draft/view?id=' . $draft->id
            ]));

            \Yii::$app->getSession()->setFlash('success', 'ModerationDraft #' . $draft->id . ' successfully executed.');

            return $this->redirect('/moderation/draft');

        }

        throw new Exception('Something went wrong while executing ModerationDraft', [
            'model' => $draft
        ]);

    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionCancel($id)
    {

        $model = $this->findModel($id);
        $model->status = ModerationDraft::STATUS_CANCELED;

        if ($model->validate() && $model->save()) {

            if ((int)$model->category === ModerationDraft::CATEGORY_MOVIES) {

                $MoviesController = new MoviesController('Movies', 'moderation');
                $MoviesModel = $MoviesController->findMoviesModeration($model->id_media);
                $MoviesController->CancelModerationStatus($MoviesModel, $model->id_media);

            } else if ((int)$model->category === ModerationDraft::CATEGORY_TVSHOWS) {

            } else if ((int)$model->category === ModerationDraft::CATEGORY_TVEPISODES) {

                $EpisodesController = new EpisodesController('Episodes', 'moderation');
                $EpisodeModel = $EpisodesController->findModel($model->id_media);
                $EpisodesController->CancelModerationStatus($EpisodeModel, $model->id_media);

            } else {

                throw new Exception('Something went wrong while canceling ModerationDraft', [
                    'model' => $model
                ]);

            }

            $this->UsersActionLogger->logAction('CancelDraft', 'Drafts', json_encode([
                'draft_id' => $model->id,
                'draft_url' => '/moderation/draft/view?id=' . $model->id
            ]));

            \Yii::$app->getSession()->setFlash('success', 'ModerationDraft #' . $model->id . ' was canceled.');

            return $this->redirect('/moderation/draft');

        }

        throw new Exception('Something went wrong while canceling ModerationDraft', [
            'model' => $model
        ]);

    }

    /**
     * Finds the ModerationDraft model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModerationDraft the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = ModerationDraft::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');

    }

}
