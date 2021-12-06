<?php

namespace backend\modules\moderation\controllers;

use backend\models\site\ModerationDraft;
use backend\models\site\ModerationDraftItems;
use backend\models\site\Movies;
use backend\models\site\MoviesModeration;
use backend\models\site\MoviesSearcj;
use backend\modules\moderation\controllers\DraftExecutors;
use common\components\log\UsersActionLog;
use common\components\movies\MoviesStorageComponent;
use common\helpers\StorageWorker;
use common\models\site\MoviesSubtitles;
use common\models\SubtitlesLanguages;
use console\jobs\metadata\MoviesMetaQueue;
use Yii;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MoviesController implements the CRUD actions for Movies model.
 */
class MoviesController extends Controller
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
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // Remember Back URL for Create Movie Page
        Url::remember(['movies/index'], 'add-movie-back');

        $searchModel = new MoviesSearcj();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->pagination->pageSize = 20;

        $dataProvider->sort = [
            'defaultOrder' => ['date_added' => SORT_DESC]
        ];

        return $this->render('index', [
            'model'       => new Movies(),
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'currentUrl' => \Yii::$app->homeUrl . \Yii::$app->request->url
        ]);
    }

    /**
     * @param $id
     *
     * @return \yii\web\Response
     */
    public function actionForceMetadataDownload($id)
    {
        $movie = Movies::find()
            ->where(['id_movie' => $id])
            ->asArray()
            ->one();

        if (empty($movie)) {
            Yii::$app->session->setFlash('error', 'Error to force meta download. Movie not found.', true);
            return $this->redirect(Url::previous());
        }

        if (empty($movie['imdb_id'])) {
            Yii::$app->session->setFlash('error', 'Error to force meta download. Movie not found.', true);
            return $this->redirect(Url::previous());
        }

        $this->forceMetadataDownload($movie['imdb_id']);

        $message = 'Movie tt'. $movie['imdb_id'] .'. Successfully added to metadata download queue.';

        Yii::$app->session->setFlash('success', $message, true);

        return $this->redirect(Url::previous());
    }

    protected function isMagnetLink($link)
    {

        $re = '/magnet:\?.+/m';

        $match = preg_match_all($re, $link, $matches, PREG_SET_ORDER, 0);

        if ($match) {
            return true;
        }

        return false;

    }

    public function actionReconvertTorrent($id)
    {

        $redirect_url = \Yii::$app->request->get('back_url', '/moderation/movies');

        $torrent_code = env('MOVIES_QUEUE_TORRENT');

        $release_title = $_POST['release-title'];
        $content = $_POST['content'];

        $is_magnet = $this->isMagnetLink($content);

        // create draft
        $draft = new ModerationDraft();

        $draft->category = ModerationDraft::CATEGORY_MOVIES;
        $draft->created_by = \Yii::$app->user->identity->id;
        $draft->status = 0;
        $draft->is_active = 1;
        $draft->created_at = time();
        $draft->id_media = $id;
        $draft->title = '#' . $id;

        if (!$draft->validate() || !$draft->save()) {
            throw new Exception('Error set movie to reconvert', [
                'model' => $draft
            ]);
        }

        $draftItem = new ModerationDraftItems();

        $draftItem->id_moderation_draft = $draft->id;
        $draftItem->controller = 'Movies';
        $draftItem->action = 'ReconvertTorrentForce';
        $draftItem->data = json_encode([
            'id_movie' => $id,
            'status_code' => $torrent_code,
            'rel_title' => $release_title,
            'type' => $is_magnet ? 1 : 0,
            'content' => $content
        ]);

        if (!$draftItem->validate() || !$draftItem->save()) {
            throw new Exception('Error set movie to reconvert with torrent force', [
                'model' => $draftItem
            ]);
        }

        $this->UsersActionLogger->logAction('Create', 'Drafts', json_encode([
            'draft_id' => $draft->id,
            'draft_url' => '/moderation/draft/view?id=' . $draft->id
        ]));

        $MoviesModeration = MoviesModeration::find()->where(['id_movie' => $id])->one();

        $DraftController = new DraftController('Draft', 'moderation');
        $DraftController->Movies = new DraftExecutors\MoviesExecutor();
        $DraftController->UsersActionLogger = new UsersActionLog();
        $DraftController->actionExecute($draft->id);

        $MoviesModeration->is_locked = 0;

        $MoviesModeration->validate();
        $MoviesModeration->save();

        \Yii::$app->getSession()->setFlash('success', 'Movie Set To Reconvert Successfully.');

        return $this->redirect($redirect_url);

    }

    function actionAddSubtitle($id_movie)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $StorageWorker = new StorageWorker();

        $contents = \Yii::$app->request->post('contents', '');
        $language = \Yii::$app->request->post('language', '');

        $modelMovie = Movies::find()->where(['id_movie' => $id_movie])->asArray()->one();

        $subtitle = MoviesSubtitles::find()->where(['id_movie' => $id_movie, 'language' => $language, 'is_moderated' => 0])->one();

        $fixed_url = "movies/{$modelMovie['storage_slug']}/subtitles/{$language}_mod.vtt";

        if ($subtitle === null) {
            $subtitle = new MoviesSubtitles();
            $subtitle->id_movie = $modelMovie['id_movie'];
            $subtitle->url = "movies/{$modelMovie['storage_slug']}/{$language}_mod";
            $subtitle->language = $language;
            $subtitle->is_moderated = 0;
            $subtitle->is_approved = 0;
            if (!$subtitle->validate() || !$subtitle->save()) {
                return [
                    'success' => false,
                    'errors' => $subtitle->getErrors()
                ];
            }
        }

        $response = $StorageWorker->SendMoviesSubtitles($contents, $subtitle->language, $modelMovie['storage_slug'], $modelMovie['shard_url']);

        usleep(1500000);

        return [
            'success' => true,
            'data' => [
                'id' => $subtitle->id,
                'url' => $fixed_url,
                'language' => $subtitle->language,
                'is_approved' => $subtitle->is_approved,
                'is_moderated' => $subtitle->is_moderated,
                'storage_message' => json_decode($response),
            ]
        ];

    }

    private function forceMetadataDownload($imdb_id)
    {
        Yii::$app->metadataDownloadQueue->push(new MoviesMetaQueue([
            'imdbId' => 'tt' . $imdb_id,
        ]));
    }

    public function actionBulkAction()
    {
        $post = Yii::$app->request->post();

        $ids    = is_array($post['ids']) ? $post['ids'] : [];
        $action = is_string($post['action']) ? $post['action'] : '';

        switch ($action) {
            case 'force-meta-download':
                foreach ($ids as $id) {
                    $model = Movies::find()
                        ->where(['id_movie' => $id])
                        ->asArray()
                        ->one();

                    if (empty($model['imdb_id'])) {
                        continue;
                    }

                    $this->forceMetadataDownload($model['imdb_id']);
                }
                break;
            default:
                return 'Bulk Action: ForceMetadataDownload - Fail';
        }

        return 'Bulk Action: ForceMetadataDownload - Success';
    }

    public function actionRedirect($id)
    {

        return $this->render('redirect', [
            'id' => $id
        ]);
    }

    public function actionCancel($id)
    {

        $redirect_url = \Yii::$app->request->get('back_url', '/moderation/movies');
        $modelMoviesModeration = $this->findMoviesModeration($id);
        $can_access = $this->ValidateModerationAccess($modelMoviesModeration);

        if (!$can_access) {
            \Yii::$app->getSession()->setFlash('error', "Movie #{$model->id_movie} being moderated by another moderator or you don't have access to it.");
            return $this->redirect($redirect_url);
        }

        \Yii::$app->getSession()->setFlash('success', 'Movie Moderation Canceled.');

        $this->CancelModerationStatus($modelMoviesModeration, $id);

        $modelModerationDraft = ModerationDraft::find()->where(['id_media' => $id, 'category' => ModerationDraft::CATEGORY_MOVIES, 'status' => ModerationDraft::STATUS_WAITING])->one();

        if ($modelModerationDraft) {
            $modelModerationDraft->status = ModerationDraft::STATUS_CANCELED;
            $modelModerationDraft->validate();
            $modelModerationDraft->save();
        }

        return $this->redirect($redirect_url);

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

        if ($draft) {
            foreach ($draft->draftItems as $draftItem) {
                $draftItem->delete();
            }
        }

        foreach ($dataObj as $objItem) {
            $draftItem = new ModerationDraftItems();
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

        $DraftController = new DraftController('Draft', 'moderation');
        $DraftController->Movies = new DraftExecutors\MoviesExecutor();
        $DraftController->UsersActionLogger = new UsersActionLog();
        $DraftController->actionExecute($draft->id);
        \Yii::$app->getSession()->setFlash('success', 'Draft Successfully Executed.');

        return $this->redirect($redirect_url);

    }

    public function actionSave()
    {

        $dataObj = json_decode(base64_decode(\Yii::$app->request->post('data')));
        $id_media = \Yii::$app->request->post('id_media');
        $title = \Yii::$app->request->post('draft_title');

        $is_draft = (int)\Yii::$app->request->post('is_draft');
        $execute = (int)\Yii::$app->request->post('execute-now');

        $redirect_url = Url::previous();

        // create draft
        $draft = ModerationDraft::find()->where([
            'id_media' => $id_media,
            'category' => ModerationDraft::CATEGORY_MOVIES,
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

        $draft->category = ModerationDraft::CATEGORY_MOVIES;
        $draft->created_by = \Yii::$app->user->identity->id;
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
            $draftItem = new ModerationDraftItems();
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

            \Yii::$app->getSession()->setFlash('success', 'Movie draft successfully saved');

        } else {

            $MoviesModeration = MoviesModeration::find()->where(['id_movie' => $id_media])->one();
            $MoviesModeration->is_locked = 1;
            $MoviesModeration->validate();
            $MoviesModeration->save();

            \Yii::$app->getSession()->setFlash('success', 'Movie Draft Sent to Approval.');

        }

        $DraftController = new DraftController('Draft', 'moderation');
        $DraftController->Movies = new DraftExecutors\MoviesExecutor();
        $DraftController->UsersActionLogger = new UsersActionLog();
        $DraftController->actionExecute($draft->id);
        \Yii::$app->getSession()->setFlash('success', 'Changes Successfully Saved.');

        return $this->redirect($redirect_url);

    }

    public function actionSubtitleFinalDelete($id_subtitle)
    {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $StorageWorker = new StorageWorker();

        $subtitle = MoviesSubtitles::findOne(['id' => $id_subtitle]);

        if ($subtitle) {

            $movie = Movies::find()->where(['id_movie' => $subtitle->id_movie])->one();

            $shard = str_ireplace('storage', 'stor', $movie->shard_url);
            $path_parts = explode('/', $subtitle->url);

            if ($subtitle->is_approved === 0 && $subtitle->is_moderated === 0) {

                $StorageWorker->AddAction('delete', json_encode([]), "{$shard}{$path_parts['0']}/{$path_parts['1']}/subtitles/{$path_parts['2']}.vtt");
                $subtitle->delete();

                return [
                    'success' => true
                ];

            }

        }

        return [
            'success' => false
        ];

    }

    /**
     * Moderate Movie Action
     *
     * @param $id
     *
     * @return string|\yii\web\Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {

        $redirect_url = \Yii::$app->request->referrer ?: '/moderation/movies';

        $this->layout = '/common.php';

        $model = $this->findModel($id);

        $modelMoviesModeration = $this->findMoviesModeration($id);

        $draft_id = (int)\Yii::$app->request->get('draft_id', 0);

        $can_access = true;
        if ($draft_id === 0) {
            $can_access = $this->ValidateModerationAccess($modelMoviesModeration);
        }

        if (!$can_access) {
            \Yii::$app->getSession()->setFlash('error', "Movie #{$model->id_movie} being moderated by another moderator or you don't have access to it.");
            return $this->redirect(urldecode($redirect_url));
        }

        if ($draft_id === 0) {
            $this->SetModerationStatus($modelMoviesModeration, $id);
        }

        $streamURL = MoviesStorageComponent::get_video_streams($id);
        $subtitles = MoviesStorageComponent::get_movie_subtitles($id);

        $subtitles_formatted = $subtitles;

        if ($draft_id > 0) {
            $ModerationDraft = ModerationDraft::find()->where(['id' => $draft_id])->one();
        } else {
            $ModerationDraft = ModerationDraft::find()->where([
                'id_media' => $id,
                'category' => ModerationDraft::CATEGORY_MOVIES,
                'status' => 0,
                'is_active' => 0,
                'created_by' => getMyId()
            ])->one();
        }

        $draft = [];
        if ($ModerationDraft) {

            $subtitles_formatted = [];

            foreach ($subtitles as $subtitle) {
                if (!array_key_exists($subtitle['language'], $subtitles_formatted)) {
                    $subtitles_formatted[$subtitle['language']] = $subtitle;
                } else {
                    if ($subtitle['is_moderated'] === '0' && $subtitle['is_approved'] === '0') {
                        $subtitles_formatted[$subtitle['language']] = $subtitle;
                    }
                }
            }

            foreach ($ModerationDraft->draftItems as $draftItem) {
                $draft[] = $draftItem->attributes;
            }
        }

        return $this->render('update', [
            'model' => $model,
            'streamURL' => $streamURL,
            'edge' => query_edge(),
            'subtitles' => array_values($subtitles_formatted),
            'movie_meta' => json_encode($model->attributes),
            'category' => ModerationDraft::CATEGORY_MOVIES,
            'back_url' => $redirect_url,
            'draft' => $draft,
            'draft_id' => $draft_id,
            'subtitles_languages' => SubtitlesLanguages::find()->asArray()->all(),
            'protection_data' => MoviesStorageComponent::movies_hotlink_protection($model->storage_slug)
        ]);

    }

    /**
     * @param $id
     *
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

    public function actionUploadBackdrop()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;

        $get = $request->get();

        $movie_id = $get['movie_id'];
        $backdrop = $get['backdrop'];

        $model = Movies::findOne(['id_movie' => $movie_id]);
        $model->backdrop = '/' . $backdrop;

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Successfully uploaded!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Something went wrong, please try again later'
            ];
        }
    }

    public function actionUploadCover()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $request = Yii::$app->request;

        $get = $request->get();

        $movie_id = $get['movie_id'];
        $poster = $get['poster'];

        $model = Movies::findOne(['id_movie' => $movie_id]);
        $model->poster = '/' . $poster;

        if ($model->save()) {
            return [
                'success' => true,
                'message' => 'Successfully uploaded!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Something went wrong, please try again later'
            ];
        }
    }

    protected function SetModerationStatus($modelMoviesModeration, $id)
    {
        $modelMoviesModeration->locked_by = \Yii::$app->user->identity->id;
        $modelMoviesModeration->locked_at = time();
        $modelMoviesModeration->is_locked = 2;

        if (!$modelMoviesModeration->validate() || !$modelMoviesModeration->save()) {
            throw new Exception("Error Updating Moderation Information", [
                'id_movie' => $id
            ]);
        }
    }

    public function CancelModerationStatus($modelMoviesModeration, $id)
    {

        $modelMoviesModeration->locked_by = null;
        $modelMoviesModeration->locked_at = null;
        $modelMoviesModeration->is_locked = 0;

        if (!$modelMoviesModeration->validate() || !$modelMoviesModeration->save()) {
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

        if ((int)$model->locked_by === \Yii::$app->user->identity->id) {
            $can_access = true;
        }

        return $can_access;

    }

    /**
     * Finds the Movies model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Movies the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Movies::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @param $id_movie
     *
     * @return MoviesModeration|null
     * @throws NotFoundHttpException
     */
    public function findMoviesModeration($id_movie)
    {
        if (($model = MoviesModeration::findOne(['id_movie' => $id_movie])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }


}
