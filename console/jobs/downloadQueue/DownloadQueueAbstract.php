<?php


namespace console\jobs\downloadQueue;

use api\modules\v1\models\imdb\ImdbBasics;
use api\modules\v1\models\imdb\ImdbEpisode;
use common\libs\Imdb\Config as ImdbConfig;
use common\helpers\Languages;
use common\libs\Imdb\Title as ImdbTitle;
use common\models\queue\Shows;
use common\models\queue\ShowsMeta as ShowsMetaQueue;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

abstract class DownloadQueueAbstract extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * @param $imdb_id
     * @return Shows|null
     */
    public function insertShow($imdb_id)
    {
        $basics = $this->showBasicMetadata($imdb_id);

        if (empty($basics)) {
            return null;
        }

        $model = new Shows();
        $model->imdb_id = $imdb_id;
        $model->title = $basics['primary_title'] ?? $basics['original_title'] ?? '';
        $model->first_air_date = implode('-', [$basics['start_year'], '01', '01']);
        $model->episode_duration = $basics['runtime_minutes'];
        $model->total_seasons = 0;
        $model->total_episodes = 0;
        $model->original_language = Languages::get_iso_by_name($basics['original_language']);

        if ($model->validate() && $model->save()) {
            return $model;
        }

        return null;
    }

    /**
     * @param $id_tvshow
     * @return bool
     * @throws \Exception
     */
    public function updateShowEpisodes($id_tvshow)
    {
        $show = Shows::find()->where(['id_tvshow' => $id_tvshow])->one();

        if (empty($show)) {
            return false;
        }

        $imdb_seasons = $this->imdbEpisodes($show->imdb_id);
        $queue_episodes = $this->downloadQueueEpisodes($show->id_tvshow);
        foreach ($imdb_seasons as $seasonNumber => $imdb_season) {
            foreach ($imdb_season as $imdb_episode) {
                // Skip episode with episode and season number less than `1`
                if (intval($imdb_episode['seasonNumber'], 10) < 1 || intval($imdb_episode['episodeNumber'], 10) < 1) {
                    continue;
                }

                $queue_episode = ArrayHelper::getValue($queue_episodes,$imdb_episode['seasonNumber'].'.'.$imdb_episode['episodeNumber'], null);
                if (empty($queue_episode)) {
                    $queue_episode = new ShowsMetaQueue();
                    $queue_episode->title = ArrayHelper::getValue($imdb_episode, 'primary_title');
                    $queue_episode->id_tvshow = $show->id_tvshow;
                    $queue_episode->season = ArrayHelper::getValue($imdb_episode, 'seasonNumber');
                    $queue_episode->episode = ArrayHelper::getValue($imdb_episode, 'episodeNumber');
                    $queue_episode->state = env('EPISODES_QUEUE_MISSING', 5);
                }

                if ($queue_episode->air_date !== implode('-', [$imdb_episode['air_date'], '01', '01'])) {
                    $queue_episode->air_date = implode('-', [$imdb_episode['air_date'], '01', '01']);
                }

                $dirty_attr = $queue_episode->getDirtyAttributes();
                if (count($dirty_attr) > 0 || $queue_episode->isNewRecord) {
                    $queue_episode->validate() && $queue_episode->save();
                }
            }
        }

        return true;
    }

    /**
     * @param $imdb_id
     * @return array|null
     */
    private function showBasicMetadata($imdb_id)
    {
        $imdb_basics = ImdbBasics::find()->where(['tconst' => $imdb_id])->one();

        // grab original language
        if (empty($imdb_basics)) {
            return null;
        }

        $response = $imdb_basics->getAttributes();
        $response['akas'] = $imdb_basics->akas;
        $response['ratings'] = $imdb_basics->ratings;
        $response['original_language'] = ImdbBasics::getOriginalLanguage($imdb_id);

        return $response;
    }

    /**
     * @param $imdb_episode
     * @param $id_tvshow
     * @return bool
     */
    protected function insertSingleEpisode($imdb_episode, $id_tvshow)
    {
        $meta = new ShowsMetaQueue;
        $meta->id_tvshow = $id_tvshow;
        $meta->season = $imdb_episode['season'];
        $meta->episode = $imdb_episode['episode'];
        $meta->state = env('EPISODES_QUEUE_WAITING_TORRENT', '4');
        $meta->air_date = date('Y-m-d', strtotime($imdb_episode['airdate']));
        $meta->title = $imdb_episode['title'];

        return $meta->validate() && $meta->save();
    }

    /**
     * @param $imdb_episode
     * @param $id_tvshow
     * @return bool
     */
    protected function updateEpisode($imdb_episode, $id_tvshow)
    {
        $meta = ShowsMetaQueue::find()->where([
            'id_tvshow' => $id_tvshow,
            'season' => $imdb_episode['season'],
            'episode' => $imdb_episode['episode']
        ])->one();

        if (empty($meta)) {
            return false;
        }

        $meta->air_date = date('Y-m-d', strtotime($imdb_episode['airdate']));
        $meta->title = $imdb_episode['title'];

        return $meta->validate() && $meta->save();
    }

    /**
     * @param $imdb_id
     * @return array
     */
    protected function imdbEpisodesParsed($imdb_id)
    {
        $imdb_id_sanitized = sanitize_imdb_id($imdb_id);

        $config = new ImdbConfig();
        $config->language = 'en-US';
        $config->usecache = true;
        $config->cache_expire = 86400;

        try {
            $imdbTitle = new ImdbTitle($imdb_id_sanitized, $config);
            return $imdbTitle->episodes();
        } catch (\Exception $e) {}

        return [];
    }

    /**
     * @deprecated
     * @param $imdb_id
     * @return array
     * @throws \Exception
     */
    protected function imdbEpisodes($imdb_id)
    {
        $imdb_episode = ImdbEpisode::find()
            ->where(['parentTconst' => $imdb_id])
            ->orderBy(['seasonNumber' => SORT_ASC, 'episodeNumber' => SORT_ASC])
            ->all();

        $response = [];

        foreach ($imdb_episode as $episode) {
            if (empty($response[$episode->seasonNumber])) {
                $response[$episode->seasonNumber] = [];
            }

            $response[$episode->seasonNumber][] = [
                'seasonNumber' => $episode->seasonNumber,
                'episodeNumber' => $episode->episodeNumber,
                'title_type' => ArrayHelper::getValue($episode, 'basics.title_type'),
                'original_title' => ArrayHelper::getValue($episode, 'basics.original_title'),
                'primary_title' => ArrayHelper::getValue($episode, 'basics.primary_title'),
                'air_date' => ArrayHelper::getValue($episode, 'basics.start_year')
            ];
        }

        return $response;
    }

    /**
     * Gets Download Queue Episodes
     * Grouped By Season and Episode
     */
    protected function downloadQueueEpisodes($id_tvshow)
    {
        $grouped_episodes = [];

        $all_episodes = ShowsMetaQueue::find()
            ->where(['id_tvshow' => $id_tvshow])
            ->all();

        foreach ($all_episodes as $episode) {
            if (empty($grouped_episodes[$episode->season])) {
                $grouped_episodes[$episode->season] = [];
            }
            $grouped_episodes[$episode->season][$episode->episode] = $episode;
        }

        return $grouped_episodes;
    }
}