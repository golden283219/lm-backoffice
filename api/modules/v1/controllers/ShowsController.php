<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\queue\GarbageCollection;
use api\modules\v1\models\site\Shows;
use api\modules\v1\models\site\ShowsEpisodes;
use api\modules\v1\models\site\ShowsEpisodesAudio;
use common\models\site\ShowsEpisodesSubtitles;
use console\jobs\metadata\ShowsMetaImdbQueue;
use Http\Client\Common\Exception\HttpClientNotFoundException;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\ContentNegotiator;
use yii\rest\ActiveController;
use yii\rest\Serializer;
use yii\web\Response;

/**
 * Class MoviesController
 */
class ShowsController extends ActiveController
{

    public $modelClass = 'api\modules\v1\resources\queue\ShowsSaveQueue';

    public $serializer = [
        'class' => Serializer::class,
        'collectionEnvelope' => 'items'
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formatParam' => 'o',
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
                'application/xml' => Response::FORMAT_XML,
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        return $actions;
    }

    /**
     * @return ActiveDataProvider
     */
    public function prepareDataProvider()
    {

        $data = $_GET;

        $filter = [];

        if (isset($_GET['filter']) && is_array($_GET['filter'])) {
            $filter = $_GET['filter'];
        }

        return new ActiveDataProvider(array(
            'query' => $this->modelClass::find()->where($filter)
        ));
    }

    public function actionAddIfMissing()
    {
        $params = Yii::$app->request->post();

        if (!$this->validateInsertData($params, [
            'id_tvshow',
            'imdb_id',
            'slug'
        ])) {
            return [
                'success' => false
            ];
        }

        $show = Shows::find()->where([
            'id_show' => $params['id_tvshow']
        ])->one();

        if ($show === null) {
            $show = new Shows();
            $show->id_show = $params['id_tvshow'];
            $show->imdb_id = $params['imdb_id'];
            $show->slug = $params['slug'];
            if (!$show->validate() || !$show->save()) {
                Yii::warning([
                    'message' => 'Unable to Add TVShow',
                    'model' => $show
                ]);

                return [
                    'success' => false
                ];
            }


        }

        $job = new ShowsMetaImdbQueue([
            'imdbId' => $params['imdb_id'],
        ]);

        Yii::$app->metadataDownloadQueue->push($job);

        return [
            'success' => true
        ];

    }

    public function actionAddOrUpdateEpisode()
    {
        $params = Yii::$app->request->post();

        if (!$this->validateInsertData($params,
            [
                'id_tvshow',
                'season',
                'episode',
                'episode_path',
                'shard',
                'is_dd',
                'original_language',
                'flag_quality',
                'release_title'
            ])
        ) {
            return [
                'success' => false
            ];
        }

        $episode = ShowsEpisodes::find()->where([
            'id_shows' => $params['id_tvshow'],
            'episode' => $params['episode'],
            'season' => $params['season']
        ])->one();

        if ($episode) {
            // Put current episode in garbage collection
            $storage_paths = ExtractStoragePath($episode->storage);
            foreach ($storage_paths as $storage_path) {
                $gc = new GarbageCollection();
                $gc->path = $storage_path;
                $gc->storage = '/' . $episode->shard . '/';
                if (!$gc->validate() || !$gc->save()) {
                    Yii::warning([
                        'message' => 'Unable to PUT in GarbageCollection',
                        'storage' => $gc->storage,
                        'path' => $gc->path
                    ]);
                }
            }

            //delete episode audio
            foreach ($episode->audio as $audio) {
                $audio->delete();
            }

            //delete subtitles
            foreach ($episode->subtitles as $subtitle) {
                $subtitle->delete();
            }
        } else {
            $episode = new ShowsEpisodes();
        }

        $storage = [];
        if (isset($params['is1080p']) && $params['is1080p'] === '1') {
            $storage['1080'] = $params['episode_path'] . '/1080p/index.m3u8';
        }

        if (isset($params['is720p']) && $params['is720p'] === '1') {
            $storage['720'] = $params['episode_path'] . '/720p/index.m3u8';
        }

        if (isset($params['is480p']) && $params['is480p'] === '1') {
            $storage['480'] = $params['episode_path'] . '/480p/index.m3u8';
        }

        if (isset($params['is360p']) && $params['is360p'] === '1') {
            $storage['360'] = $params['episode_path'] . '/360p/index.m3u8';
        }

        //update information fields
        $episode->storage = $storage;
        $episode->id_shows = $params['id_tvshow'];
        $episode->episode = $params['episode'];
        $episode->season = $params['season'];
        $episode->flag_quality = $params['flag_quality'];
        $episode->rel_title = $params['release_title'];
        $episode->shard = $params['shard'];

        //reset tech fields
        $episode->subtitles_state = 0;
        $episode->has_metadata = 0;
        $episode->is_locked = 0;
        $episode->quality_approved = 0;
        $episode->finalized_subs = 0;
        $episode->have_all_subs = 0;
        $episode->locked_by = null;
        $episode->locked_at = null;

        // After save episode will not be new record
        // So we need to check episode new record or not before save
        $isNewRecord = $episode->isNewRecord;

        if ($episode->save()) {
            if ($isNewRecord) {
                // Trigger new episode event
                $episode->trigger(ShowsEpisodes::EVENT_NEW_EPISODE);
            }

            $show = Shows::find()
                ->where(['id_show' => $params['id_tvshow']])
                ->asArray()
                ->one();

            if (!empty($show)) {
                Yii::$app->metadataDownloadQueue->push(new ShowsMetaImdbQueue([
                    'imdbId' => $show['imdb_id'],
                ]));
            }
        }

        if ($params['is_dd'] === '1') {
            // insert AC3 DD Audio
            $dd_ac3 = new ShowsEpisodesAudio();
            $dd_ac3->type = 1;
            $dd_ac3->shard = $episode->shard;
            $dd_ac3->id_episode = $episode->id;
            $dd_ac3->storage_path = $params['episode_path'] . '/dd-audio-ac3/audio.m3u8';
            $dd_ac3->lang_iso_639 = $params['original_language'];
            $dd_ac3->original = 1;
            if (!$dd_ac3->validate() || !$dd_ac3->save()) {
                Yii::warning([
                    'message' => 'Unable to Insert Episode Audio',
                    'model' => $dd_ac3
                ]);
            }

            $dd_aac = new ShowsEpisodesAudio();
            $dd_aac->type = 0;
            $dd_aac->shard = $episode->shard;
            $dd_aac->id_episode = $episode->id;
            $dd_aac->storage_path = $params['episode_path'] . '/dd-audio/audio.m3u8';
            $dd_aac->lang_iso_639 = $params['original_language'];
            $dd_aac->original = 1;
            if (!$dd_aac->validate() || !$dd_aac->save()) {
                Yii::warning([
                    'message' => 'Unable to Insert Episode Audio',
                    'model' => $dd_aac
                ]);
            }
        }

        // do subtitles save
        if (isset($params['subs'])) {
            $this->batchAddSubtitles($episode->id, $params['subs']);
        }

        return [
            'success' => true
        ];
    }

    public function actionUpdateEpisodeStream($id_episode)
    {
        $allowed_fields = ['360', '480', '720', '1080'];

        $episode = ShowsEpisodes::find()->where(['id' => $id_episode])->one();

        if (null === $episode) {
            throw new HttpClientNotFoundException('Requested episode id not found', 404);
        }

        $storage = $episode->storage;
        $storage_updates = Yii::$app->request->post();

        foreach ($storage_updates as $key => $storage_update) {
            if (in_array($key, $allowed_fields)) {
                $storage[$key] = $storage_update;
            }
        }

        $episode->storage = $storage;

        return $episode->save();

    }

    public function validateInsertData($params, $required = [])
    {
        foreach ($required as $item) {
            if (!isset($params[$item])) {
                return false;
            }
        }
        return true;
    }

    public function batchAddSubtitles($id_episode, $subs_decoded)
    {
        try {
            foreach ($subs_decoded as $subtitle) {
                $model = ShowsEpisodesSubtitles::find()->where(['id_episode' => $id_episode, 'isoCode' => $subtitle['lang_iso']])->one();

                if ($model) {
                    $model->delete();
                }

                $model = new ShowsEpisodesSubtitles();
                $model->id_episode = $id_episode;
                $model->languageName = $subtitle['language'];
                $model->isoCode = $subtitle['lang_iso'];
                $model->shard = $subtitle['shard_url'];
                $model->storagePath = $subtitle['path'];
                $model->is_approved = 1;
                $model->is_moderated = 1;

                if (!$model->validate() || !$model->save()) {
                    return false;
                }
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'BatchShowsAddSubtitles');
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'BatchShowsAddSubtitles');
        }

        return true;
    }
}
