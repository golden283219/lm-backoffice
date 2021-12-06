<?php

namespace console\jobs\metadata;

use common\components\imageStorage\ImageStorage;
use common\helpers\Tmdb;
use common\libs\Imdb\Title as ImdbTitle;
use common\models\ShowsRelated;
use common\models\site\Genres;
use common\models\site\ShowsCache;
use common\models\site\ShowsCast;
use common\models\site\ShowsEpisodes;
use common\models\site\ShowsGenres;
use common\models\site\ShowsSeasons;
use console\models\site\Shows;
use Exception;
use GuzzleHttp\Client;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class ShowsMetaImdbQueue extends AbstractMetaData
{
    const DOMAIN = 'https://www.imdb.com';

    /**
     * @var
     */
    public $imdbId;

    /**
     * @var ImdbTitle
     */
    private $ImdbTitle;

    public function execute($queue)
    {
        $imdb_id = $this->get_imdb_id();

        $show = Shows::find()
            ->where(['imdb_id' => 'tt' . $imdb_id])
            ->one();

        $shows_queue = \backend\models\queue\Shows::find()
            ->where(['id_tvshow' => $show->id_show])
            ->one();

        if (empty($show)) {
            return false;
        }

        $this->ImageStorage = new ImageStorage();
        $this->ImdbTitle = $this->initImdbTitle($this->imdbId);

        $IMDbCollectedData = $this->IMDbCollectTitleData();

        if (empty($IMDbCollectedData)) {
            return false;
        }

        $imdb_genres = $this->ImdbTitle->genres();

        $imdb_director = $this->ImdbTitle->director();
        $imdb_cast = $this->ImdbTitle->cast(true);
        $imdb_episodes = $this->ImdbTitle->episodes();
        $imdb_related = $this->ImdbTitle->getRelated();
        if (!empty($shows_queue) && !empty($shows_queue->tvmaze_id)) {
            $tvmaze_seasons = $this->getTvmazeEpisodes($shows_queue->tvmaze_id);
        }

        foreach ($IMDbCollectedData as $key => $value) {
            $show->$key = $value;
        }

        yii2_ping_connection();
        if (!$show->validate() || !$show->save()) {
            Yii::error(json_encode($show->errors, JSON_PRETTY_PRINT), 'ShowsMetaImdbQueue:shows->save()');
            return false;
        }

        $genres = $this->saveShowGenres($imdb_genres, $show->id_show);
        $cast = $this->saveShowCast($imdb_cast, $imdb_director, $show->id_show);

        $show->cast = $cast;
        $show->genres = $genres;
        $show->is_active = 1;
        $show->has_metadata = 1;
        $show->save();

        if (!empty($tvmaze_seasons)) {
            $this->updateShowEpisodesTVMaze($tvmaze_seasons, $show->id_show);
        } else {
            $this->updateShowEpisodes($imdb_episodes, $show->id_show);
        }

        $this->updateShowsCache($show->id_show);
        $this->updateRelatedShows($imdb_related, $show->id_show);

        return true;
    }

    /**
     * @return string
     */
    private function get_imdb_id()
    {
        return substr($this->imdbId, 0, 2) === 'tt' ? substr($this->imdbId, 2) : $this->imdbId;
    }

    /**
     * @param $tvmaze_id int
     *
     * @return array|mixed
     */
    private function getTvmazeEpisodes($tvmaze_id)
    {
        $client = new Client();
        $seasons = array();
        try {
            $response = $client->get('https://api.tvmaze.com/shows/' . $tvmaze_id . '/episodes');
            $episodes = Json::decode($response->getBody());
            foreach ($episodes as $episode) {
                if (empty($seasons[$episode['season']])) {
                    $seasons[$episode['season']] = [];
                }
                $seasons[$episode['season']][$episode['number']] = $episode;
            }
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), 'getTvmazeEpisodes()');
        }

        return $seasons;
    }

    /**
     * Save All Cast Information to Database
     *
     * @param array $cast
     * @param array $directors
     * @param $id_show
     *d
     *
     * @return array
     */
    private function saveShowCast(array $cast, array $directors, $id_show)
    {
        $cast_cached = [];
        $db_cast = $this->saveCastInfo($cast, 'actor');
        $db_directors = $this->saveCastInfo($directors, 'director');
        ShowsCast::deleteAll(['id_show' => $id_show]);

        foreach ($db_cast as $db_cast_item) {
            $model = new ShowsCast();
            $model->id_show = $id_show;
            $model->id_cast = $db_cast_item['id'];
            $model->role = 'actor';
            $model->hero = $db_cast_item['hero'];
            $model->save();

            if (count($cast_cached) < 8 && !empty($db_cast_item['id'])) {
                $cast_cached[] = [
                    'id' => $db_cast_item['id'],
                    'hero' => $db_cast_item['hero'],
                    'name' => $db_cast_item['name'],
                    'role' => $db_cast_item['role'],
                    'picture_url' => $db_cast_item['picture_url']
                ];
            }
        }

        if (count($db_directors) > 0) {
            $cast_cached[] = [
                'id' => $db_directors['0']['id'],
                'hero' => $db_directors['0']['hero'],
                'name' => $db_directors['0']['name'],
                'role' => $db_directors['0']['role'],
                'picture_url' => $db_directors['0']['picture_url']
            ];
        }

        return $cast_cached;
    }

    /**
     * @param $id_show
     *
     * @return bool
     */
    private function updateShowsCache($id_show)
    {
        $showsCache = ShowsCache::find()
            ->where(['id_show' => $id_show])
            ->one();

        if ($showsCache === null) {
            $showsCache = new ShowsCache;
            $showsCache->id_show = $id_show;
        }

        $latestEpisode = ShowsEpisodes::find()
            ->where(['id_shows' => $id_show])
            ->orderBy(['air_date' => SORT_DESC])
            ->asArray()
            ->one();

        if (empty($latestEpisode)) {
            return false;
        }

        $showsCache->latest_episode_air_date = $latestEpisode['air_date'];
        $showsCache->latest_season = $latestEpisode['season'];
        $showsCache->latest_season_episodes_qty = $latestEpisode['season'];

        return $showsCache->validate() && $showsCache->save();
    }

    /**
     * @param $genres
     * @param $id_show
     *
     * @return array
     */
    private function saveShowGenres($genres, $id_show)
    {
        $show_genres = [];
        $db_genres = Genres::find()->asArray()->all();

        // delete all shows genres
        ShowsGenres::deleteAll(['id_show' => $id_show]);
        foreach ($db_genres as $db_genre) {
            if (in_array($db_genre['title'], $genres)) {
                $show_genres[] = [
                    'title' => $db_genre['title'],
                    'id' => $db_genre['id'],
                ];

                $model = new ShowsGenres();
                $model->id_show = $id_show;
                $model->id_genre = $db_genre['id'];
                $model->save();
            }
        }

        return $show_genres;
    }

    /**
     * @param array $episodes
     * @param $id_show
     *
     * @throws Exception
     */
    private function updateShowEpisodesTVMaze(array $episodes, $id_show)
    {
        $seasons = [];
        $_seasons = ShowsSeasons::find()->where(['id_show' => $id_show])->all();

        foreach ($_seasons as $_season) {
            $seasons[$_season->season] = $_season;
        }

        foreach (ShowsEpisodes::find()->where(['id_shows' => $id_show])->batch(100) as $shows_episodes) {
            foreach ($shows_episodes as $shows_episode) {

                // save season basic information if missing
                if (empty($seasons[$shows_episode->season])) {
                    $showSeason = new ShowsSeasons();
                    $showSeason->id_show = $id_show;
                    $showSeason->season = $shows_episode->season;
                    $showSeason->title = 'Season ' . $shows_episode->season;
                    $showSeason->save();

                    $seasons[$showSeason->season] = $showSeason;
                }

                if (!empty($episodes[$shows_episode->season]) && !empty($episodes[$shows_episode->season][$shows_episode->episode])) {
                    $air_date = ArrayHelper::getValue($episodes[$shows_episode->season][$shows_episode->episode], 'airdate', null);

                    $missing_meta = empty($shows_episode->title) || empty($shows_episode->air_date) || empty($shows_episode->description);
                    $require_meta_updated= $shows_episode->air_date !== $air_date && !empty($air_date);

                    if ($missing_meta || $require_meta_updated) {
                        $shows_episode->title = ArrayHelper::getValue($episodes[$shows_episode->season][$shows_episode->episode], 'title', '');

                        if (empty($air_date) || strtotime($air_date) > time()) {
                            $air_date = date('Y-m-d', time());
                        }

                        $shows_episode->air_date = $air_date;
                        $shows_episode->description = ArrayHelper::getValue($episodes[$shows_episode->season][$shows_episode->episode], "summary", '');

                        $shows_episode->has_metadata = 1;
                        $shows_episode->is_active = 1;
                        if (!$shows_episode->save()) {
                            Yii::error('Unable to save episode: \n' . json_encode($shows_episode->errors, JSON_PRETTY_PRINT), 'ShowsMetaImdbQueue');
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $episodes
     * @param $id_show
     *
     * @throws Exception
     */
    private function updateShowEpisodes(array $episodes, $id_show)
    {
        $seasons = [];
        $_seasons = ShowsSeasons::find()->where(['id_show' => $id_show])->all();

        foreach ($_seasons as $_season) {
            $seasons[$_season->season] = $_season;
        }

        foreach (ShowsEpisodes::find()->where(['id_shows' => $id_show])->batch(100) as $shows_episodes) {
            foreach ($shows_episodes as $shows_episode) {

                // save season basic information if missing
                if (empty($seasons[$shows_episode->season])) {
                    $showSeason = new ShowsSeasons();
                    $showSeason->id_show = $id_show;
                    $showSeason->season = $shows_episode->season;
                    $showSeason->title = 'Season ' . $shows_episode->season;
                    $showSeason->save();

                    $seasons[$showSeason->season] = $showSeason;
                }

                if (!empty($episodes[$shows_episode->season]) && !empty($episodes[$shows_episode->season][$shows_episode->episode])) {
                    if (empty($shows_episode->title) || empty($shows_episode->air_date) || empty($shows_episode->description)) {
                        $shows_episode->title = ArrayHelper::getValue($episodes[$shows_episode->season][$shows_episode->episode], 'title', '');

                        $air_date = ArrayHelper::getValue($episodes[$shows_episode->season][$shows_episode->episode], 'airdate', null);

                        if (empty($air_date) || strtotime($air_date) > time()) {
                            $air_date = date('Y-m-d', time());
                        }

                        $shows_episode->air_date = date('Y-m-d', strtotime($air_date));
                        $shows_episode->description = ArrayHelper::getValue($episodes[$shows_episode->season][$shows_episode->episode], "plot", '');

                        $shows_episode->has_metadata = 1;
                        $shows_episode->is_active = 1;
                        $shows_episode->save();
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    private function IMDbCollectTitleData()
    {
        if (empty(($this->ImdbTitle->title()))) {
            return [];
        }

        $collectedData = [];

        $collectedData['title'] = $this->getIMDbTitle('tt'.$this->get_imdb_id()) ??
            $this->ImdbTitle->title() ??
            $this->ImdbTitle->orig_title();
        $collectedData['title'] = html_entity_decode($collectedData['title'], ENT_QUOTES | ENT_XML1, 'UTF-8');
        $collectedData['year'] = $this->ImdbTitle->year() ?? 0;
        $collectedData['original_lang'] = $this->ImdbTitle->language();
        $collectedData['first_air_date'] = $this->ImdbTitle->episodeAirDate();

        $country = $this->ImdbTitle->country();
        $collectedData['country'] = !empty($country) && !empty($country['0']) ? $country['0'] : '';

        $collectedData['imdb_rating'] = $this->ImdbTitle->rating();
        if (empty($collectedData['imdb_rating'])) {
            $collectedData['imdb_rating'] = 0;
        }

        $collectedData['duration'] = $this->ImdbTitle->runtime() ?? '';


        $plots = $this->ImdbTitle->plot_split();
        if (!empty($plots)) {
            $collectedData['description'] = $plots[0]['plot'];
        } else {
            $collectedData['description'] = $this->ImdbTitle->synopsis();
        }

        $imgPoster = http_get_contents($this->ImdbTitle->photo(false));
        $uploadInfo = $this->ImageStorage->handlePosterUpload($imgPoster);
        if ($uploadInfo['success'] == true && !empty($uploadInfo['path'])) {
            $collectedData['poster'] = '/' . $uploadInfo['path'];
        }

        $site = $this->ImdbTitle->officialSites();
        if (!empty($site) && !empty($site['0']) && !empty($site[0]['url'])) {
            $collectedData['homepage'] = $site[0]['url'];
        }

        $tmdbShow = Tmdb::findBy($this->imdbId, 'imdb_id', 'en-US');

        if (!empty($tmdbShow) && !empty($tmdbShow['backdrop_path'])) {
            $backdrop_contents = Tmdb::getBackdropContents($tmdbShow);
            if (!empty($backdrop_contents)) {
                $collectedData['backdrop'] = $this->upload_backdrop($backdrop_contents);
            }
        }

        if (empty($collectedData['poster']) && !empty($tmdbShow) && !empty($tmdbShow['poster_path'])) {
            $poster_contents = Tmdb::getPosterContents($tmdbShow);
            if (!empty($poster_contents)) {
                $collectedData['poster'] = $this->upload_poster($poster_contents);
            }
        }

        if (!empty($tmdbShow) && !empty($tmdbShow['id'])) {
            $collectedData['youtube'] = Tmdb::getTrailer('tv', $tmdbShow['id']);
        }

        return $collectedData;
    }

    /**
     * @param $related_shows_imdb
     * @param $id_show
     *
     * @throws Throwable
     */
    private function updateRelatedShows($related_shows_imdb, $id_show)
    {
        // Delete old relations
        ShowsRelated::deleteAll('id_show = ' . $id_show);

        foreach ($related_shows_imdb as $related_show_imdb) {
            $this->insertRelatedShow($id_show, $related_show_imdb);
        }
    }

    /**
     * Insert Related Show
     *
     * @param $id_show
     * @param $imdb_id
     *
     * @throws Throwable
     */
    private function insertRelatedShow($id_show, $imdb_id)
    {
        $show = Shows::findOne(['imdb_id' => $imdb_id]);

        if ($show) {
            // Add relation
            $relatedShow = new ShowsRelated();
            $relatedShow->related_id_show = $show->id_show;
            $relatedShow->id_show = $id_show;
            $relatedShow->insert();
        }
    }
}
