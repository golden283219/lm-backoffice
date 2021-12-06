<?php


namespace console\jobs\downloadQueue;

use common\helpers\Languages;
use common\helpers\TorrentsRegistryHelper;
use common\models\queue\Shows;
use common\models\queue\ShowsMeta;
use common\models\site\ShowsEpisodes;
use yii\helpers\ArrayHelper;

class InsertMultiMagnet extends DownloadQueueAbstract
{
    public $showInfo;

    public function execute($queue)
    {
        $show = Shows::find()->where(['imdb_id' => $this->showInfo['imdb_id']])->one();

        if (empty($show)) {
            $show = $this->insertShow($this->showInfo['imdb_id']);

            if (empty($show)) {
                return false;
            }
        }

        if (!in_array($show->original_language, Languages::suggested_languages)) {
            return false;
        }

        $seasonNumber = intval($this->showInfo['season'], 10);

        // if no season number skip that magnet assignment
        if (empty($seasonNumber)) {
            return false;
        }

        $torrentRegistry = new TorrentsRegistryHelper(['priority' => 99]);

        $queue_episodes = $this->downloadQueueEpisodes($show->id_tvshow);
        $imdb_seasons = $this->imdbEpisodesParsed($show->imdb_id);

        $imdb_season = ArrayHelper::getValue($imdb_seasons, "$seasonNumber", []);
        $queue_season = ArrayHelper::getValue($queue_episodes, "$seasonNumber", []);

        $has_updated = false;
        foreach ($imdb_season as $imdb_episode_number => $imdb_episode) {
            if (empty($queue_season[$imdb_episode_number])) {
                $this->insertSingleEpisode($imdb_episode, $show->id_tvshow);
                $has_updated = true;
                continue;
            }

            $imdb_episode_airdate = date('Y-m-d', strtotime($imdb_episode['airdate']));
            if (empty($queue_season['air_date']) || $imdb_episode_airdate !== $queue_season['air_date']) {
                $this->updateEpisode($imdb_episode, $show->id_tvshow);
                $has_updated = true;
            }
        }

        if ($has_updated) {
            $queue_episodes = $this->downloadQueueEpisodes($show->id_tvshow);
            $queue_season = ArrayHelper::getValue($queue_episodes, "$seasonNumber", []);
        }

        foreach ($queue_season as $queue_episode) {
            if (intval($queue_episode['state'], 10) === 3) {
                continue;
            }

            $site_episode = ShowsEpisodes::find()->where([
                'id_shows' => $show->id_tvshow,
                'season' => $queue_episode['season'],
                'episode' =>$queue_episode['episode']
            ])->one();

            $flag_quality = ArrayHelper::getValue($site_episode, 'flag_quality', 0);

            if ($flag_quality >= intval($this->showInfo['flag_quality'],10)) {
                continue;
            }

            // do all checks and only then insert episode
            $torrentRegistry->insert($this->showInfo['link'], $queue_episode['id_meta'], null, $this->showInfo['relTitle'], $this->showInfo['flag_quality']);
        }

        return true;
    }
}
