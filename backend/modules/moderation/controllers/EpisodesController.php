<?php

namespace backend\modules\moderation\controllers;

use Yii;
use common\models\ShowsEpisodes;
use common\helpers\StorageWorker;
use common\models\ShowsEpisodesSubtitles;
use common\models\ShowsEpisodesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\modules\moderation\models\ModerationDraft;
use backend\modules\moderation\models\DraftItems;
use common\components\log\UsersActionLog;
use common\models\SubtitlesLanguages;
use yii\db\Exception;
use common\helpers;
use backend\models\queue\ShowsMeta;
use yii\helpers\Url;

class EpisodesController extends Controller
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

    public function actionIndex()
    {
        $searchModel = new ShowsEpisodesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->query->andWhere(['or',
            ['locked_by' => null],
            ['locked_by' => \Yii::$app->user->identity->id]
        ]);

        $dataProvider->pagination->pageSize = 10;

        Url::remember();

        $dataProvider->sort = [
            'defaultOrder' => ['air_date' => SORT_DESC]
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentUrl' => \Yii::$app->homeUrl . \Yii::$app->request->url
        ]);

    }

    private function updateEpisodesStatus($episodesMap, $priority = 3)
    {
        foreach ($episodesMap as $episodeMap) {
            $episode = ShowsMeta::find()->where(['id_meta' => $episodeMap['id_meta']])->one();

            if ($episode !== null) {
                $episode->state = env('EPISODES_QUEUE_WAITING_TORRENT', 4);
                $episode->torrent_blob = null;
                $episode->rel_title = $episodeMap['rel_title'];
                $episode->type = 4;
                $episode->priority = $priority;
                $episode->save();
            }
        }

        return true;
    }

    public function actionExecuteDraft($id_draft)
    {

        $dataObj = json_decode(base64_decode(\Yii::$app->request->post('data')));
        $id_media = \Yii::$app->request->post('id_media');
        $title = \Yii::$app->request->post('draft_title');

        $is_draft = (int)\Yii::$app->request->post('is_draft');
        $execute = (int)\Yii::$app->request->post('execute-now');

        $redirect_url = \Yii::$app->request->get('back_url', '/moderation/draft');


        // Try To Find Draft
        $draft = ModerationDraft::find()->where(['id' => $id_draft])->one();

        if (null === $draft) {
            throw new Exception('Error editing users draft');
        }

        foreach ($draft->draftItems as $draftItem) {
            $draftItem->delete();
        }

        foreach ($dataObj as $objItem) {
            $draftItem = new DraftItems();
            $draftItem->id_moderation_draft = $draft->id;
            $draftItem->controller = $objItem->controller;
            $draftItem->action = $objItem->action;
            $draftItem->data = json_encode($objItem->data);

            if (!$draftItem->validate() || !$draftItem->save()) {
                throw new Exception('Error set movie to reconvert', [
                    'model' => $draftItem
                ]);
            }
        }


        if (\Yii::$app->user->can('super_moderator')) {
            $DraftController = new DraftController('Draft', 'moderation');
            $DraftController->Episodes = new DraftExecutors\EpisodesExecutor();
            $DraftController->UsersActionLogger = new UsersActionLog();
            $DraftController->actionExecute($draft->id);
            \Yii::$app->getSession()->setFlash('success', 'Draft Successfully Executed.');
        }

        return $this->redirect($redirect_url);

    }

    public function actionSave()
    {

        $dataObj = json_decode(base64_decode(\Yii::$app->request->post('data')));
        $id_media = \Yii::$app->request->post('id_media');
        $title = \Yii::$app->request->post('draft_title');

        $is_draft = (int)\Yii::$app->request->post('is_draft');
        $execute = (int)\Yii::$app->request->post('execute-now');

        $redirect_url = \Yii::$app->request->get('back_url', '/moderation/episodes');


        // create draft
        $draft = ModerationDraft::find()->where([
            'id_media' => $id_media,
            'category' => ModerationDraft::CATEGORY_TVEPISODES,
            'status' => 0,
            'is_active' => 0,
            'created_by' => getMyId()
        ])->one();

        if ($draft) {
            foreach ($draft->draftItems as $draftItem) {
                $draftItem->delete();
            }
        }

        if (null === $draft) {
            $draft = new ModerationDraft();
        }

        $draft->category = ModerationDraft::CATEGORY_TVEPISODES;
        $draft->created_by = getMyId();
        $draft->status = 0;
        $draft->is_active = 1;

        if ($is_draft) {
            $draft->is_active = 0;
        }

        $draft->created_at = time();
        $draft->id_media = $id_media;
        $draft->title = $title;

        if (!$draft->validate() || !$draft->save()) {
            throw new Exception('Error Saving Draft', [
                'model' => $draft
            ]);
        }

        foreach ($dataObj as $objItem) {
            $draftItem = new DraftItems();
            $draftItem->id_moderation_draft = $draft->id;
            $draftItem->controller = $objItem->controller;
            $draftItem->action = $objItem->action;
            $draftItem->data = json_encode($objItem->data);

            if (!$draftItem->validate() || !$draftItem->save()) {
                throw new Exception('Error set movie to reconvert', [
                    'model' => $draftItem
                ]);
            }
        }

        $this->UsersActionLogger->logAction('Create', 'Drafts', json_encode([
            'draft_id' => $draft->id,
            'draft_url' => '/moderation/draft/view?id=' . $draft->id
        ]));

        if ($is_draft) {

            \Yii::$app->getSession()->setFlash('success', 'Episode draft successfully saved');

        } else {

            $Episode = ShowsEpisodes::find()->where(['id' => $id_media])->one();
            $Episode->is_locked = 1;
            $Episode->validate();
            $Episode->save();

            \Yii::$app->getSession()->setFlash('success', 'Episode Draft Sent to Approval.');

        }

        if (\Yii::$app->user->can('super_moderator') && $is_draft === 0 && $execute === 1) {
            $DraftController = new DraftController('Draft', 'moderation');
            $DraftController->Episodes = new DraftExecutors\EpisodesExecutor();
            $DraftController->UsersActionLogger = new UsersActionLog();
            $DraftController->actionExecute($draft->id);
            \Yii::$app->getSession()->setFlash('success', 'Changes Successfully Saved.');
        }

        return $this->redirect($redirect_url);

    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new ShowsEpisodes();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $this->layout = '/common.php';

        $model = $this->findModel($id);

        $can_access = $this->ValidateModerationAccess($model);

        if (!$can_access) {
            \Yii::$app->getSession()->setFlash('error', "Episode #{$model->id} being moderated by another moderator or you don't have access to it.");
            return $this->redirect(Url::previous());
        }

        $this->SetModerationStatus($model, $id);

        $subtitles = $this->GetEpisodeSubtitles($id);
        $subtitles_formatted = $subtitles;
        $subtitles_languages = SubtitlesLanguages::find()->asArray()->all();

        return $this->render('update', [
            'shows_meta' => find_id_meta_by_id_episode($model->id),
            'subtitles_languages' => $subtitles_languages,
            'edge' => query_edge(),
            'back_url' => Url::previous(),
            'subtitles' => array_values($subtitles_formatted),
            'protection_data' => $this->ShowProtectionData($model->show->slug),
            'model' => $model,
        ]);
    }

    public function actionCancel($id)
    {
        $model = $this->findModel($id);

        $can_access = $this->ValidateModerationAccess($model);

        if (!$can_access) {
            \Yii::$app->getSession()->setFlash('error', "Movie #{$model->id_episode} being moderated by another moderator or you don't have access to it.");
            return $this->redirect(Url::previous());
        }

        \Yii::$app->getSession()->setFlash('success', 'Episode Moderation Canceled.');

        $this->CancelModerationStatus($model, $id);

        $modelModerationDraft = ModerationDraft::find()->where(['id_media' => $id, 'category' => ModerationDraft::CATEGORY_TVEPISODES, 'status' => ModerationDraft::STATUS_WAITING])->one();

        if ($modelModerationDraft) {
            $modelModerationDraft->status = ModerationDraft::STATUS_CANCELED;
            $modelModerationDraft->validate();
            $modelModerationDraft->save();
        }

        return $this->redirect(Url::previous());

    }

    protected function SetModerationStatus($model, $id)
    {

        $model->locked_by = \Yii::$app->user->identity->id;
        $model->locked_at = time();
        $model->is_locked = 2;

        if (!$model->validate() || !$model->save()) {
            throw new Exception("Error Updating Moderation Information", [
                'id_movie' => $id
            ]);
        }

    }

    protected function ShowProtectionData($slug)
    {

        $client_ip = $_SERVER['REMOTE_ADDR'];

        if (YII_ENV === 'dev') {
            $client_ip = '141.98.252.167';
        }

        $expires = time() + 6 * 3600;

        $hash = helpers\Generic::base64_md5_hash($slug . $expires . $client_ip . 'G&59a*6dEL_8');

        return (object)[
            'hash' => $hash,
            'expires' => $expires
        ];

    }

    public function CancelModerationStatus($model, $id)
    {

        $model->locked_by = null;
        $model->locked_at = null;
        $model->is_locked = 0;

        if (!$model->validate() || !$model->save()) {
            throw new Exception("Error Updating Moderation Information", [
                'id_movie' => $id
            ]);
        }

    }

    protected function ValidateModerationAccess($model)
    {

        $can_access = true;

        if ((bool)$model->is_locked) {
            $can_access = \Yii::$app->user->can('administrator');
        }

        if ((int)$model->locked_by === \Yii::$app->user->identity->id && $model->is_locked !== 1) {
            $can_access = true;
        }

        return $can_access;

    }

    protected function GetEpisodeSubtitles($id)
    {

        return ShowsEpisodesSubtitles::find()->where(['id_episode' => $id])->asArray()->all();

    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function findModel($id)
    {
        if (($model = ShowsEpisodes::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
