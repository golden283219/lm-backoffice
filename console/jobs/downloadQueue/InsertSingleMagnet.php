<?php


namespace console\jobs\downloadQueue;

use common\helpers\Languages;
use common\models\queue\Shows;
use common\models\ShowsMeta;
use common\models\site\ShowsEpisodes;
use yii\helpers\ArrayHelper;

class InsertSingleMagnet extends DownloadQueueAbstract
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

        // Episode in Download QueueDB
        $shows_meta = ShowsMeta::find()->where([
            'id_tvshow' => $show->id_tvshow,
            'season' => $this->showInfo['season'],
            'episode' => $this->showInfo['episode']
        ])->one();


        $imdb_seasons = $this->imdbEpisodesParsed(sanitize_imdb_id($this->showInfo['imdb_id']));
        $imdb_episode = ArrayHelper::getValue($imdb_seasons, "{$this->showInfo['season']}.{$this->showInfo['episode']}");

        if (empty($imdb_episode)) {
            return false;
        }

        if (empty($shows_meta)) {
            $this->insertSingleEpisode($imdb_episode, $show->id_tvshow);

            $shows_meta = ShowsMeta::find()->where([
                'id_tvshow' => $show->id_tvshow,
                'season' => $this->showInfo['season'],
                'episode' => $this->showInfo['episode']
            ])->one();

            if (empty($shows_meta)) {
                return false;
            }
        }

        $imdb_episode_airdate = date('Y-m-d', strtotime($imdb_episode['airdate']));
        if (empty($shows_meta['air_date']) || $shows_meta['air_date'] !== $imdb_episode_airdate) {
            $this->updateEpisode($imdb_episode, $show->id_tvshow);
        }

        // Episode on SiteDB
        $shows_episodes = ShowsEpisodes::find()->where([
            'id_shows' => $show->id_tvshow,
            'season' => $this->showInfo['season'],
            'episode' => $this->showInfo['episode']
        ])->one();


        // Current Episode Flag Quality if exists on site database
        $flag_quality = ArrayHelper::getValue($shows_episodes, 'flag_quality', 0);

        // if current flag quality is higher or same, skip
        if ($flag_quality >= intval($this->showInfo['flag_quality'], 10)) {
            return false;
        }

        // skip videos if its being converted
        if ($shows_meta->state == '3') {
            return false;
        }

        $shows_meta->type = 1;
        $shows_meta->rel_title = ArrayHelper::getValue($this->showInfo, 'rel_title', '');
        $shows_meta->state = ArrayHelper::getValue($this->showInfo, 'state', env('EPISODES_QUEUE_WAITING_TORRENT', 4));
        $shows_meta->torrent_blob = $this->showInfo['link'];
        $shows_meta->flag_quality = intval($this->showInfo['flag_quality'], 10);
        $shows_meta->priority = 99;

        if ($shows_meta->validate() && $shows_meta->save()) {
            return true;
        }

        return false;
    }
}
