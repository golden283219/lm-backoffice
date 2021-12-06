<?php

namespace console\models\tvmazeQueue;

use console\models\queue\Shows as ShowsQueue;
use common\models\queue\ShowsMeta as ShowsMetaQueue;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use TVMaze\API\Client as TVMaze;
use yii\base\BaseObject;
use yii\helpers\Console;

abstract class AbstractTvMazeQueue extends BaseObject implements \yii\queue\JobInterface
{
    /**
     * Updates Or Adds Single show existing episodes
     * from tv maze
     *
     * @param array $config
     *
     * @return bool
     */
    protected function UpdateShow(array $config = [])
    {
        if (empty($config['tvmaze_id']) && empty($config['imdb_id']) && empty($config['tvdb_id'])) {
            return false;
        }

        $tvMaze = new TVMaze();

        try {
            $tvMazeShow = $tvMaze->shows->getById($config['tvmaze_id']);

            if (empty($tvMazeShow) && !empty($config['imdb_id'])) {
                $tvMazeShow = $tvMaze->shows->getByIMDB($config['imdb_id']);
            }

            if (empty($tvMazeShow) && !empty($config['tvdb_id'])) {
                $tvMazeShow = $tvMaze->shows->getByTVDB($config['tvdb_id']);
            }
        } catch (\Exception $e) {
            print($e->getMessage());
        }

        if (empty($tvMazeShow)) {
            return false;
        }

        $showQuery = ShowsQueue::find()
            ->where(['tvmaze_id' => $tvMazeShow->id]);
        if (!empty($config['imdb_id'])) {
            $showQuery->orWhere(['imdb_id' => $config['imdb_id']]);
        }

        $show = $showQuery->one();

        if (empty($show)) {
            $show = new ShowsQueue;
            $show->title = $tvMazeShow->name;
            $show->first_air_date = $tvMazeShow->premiered->format('Y-m-d');
            $show->original_language = $this->getLangCodeByName($tvMazeShow->language);
            $show->episode_duration = $tvMazeShow->runtime;
            $show->in_production = $tvMazeShow->status === 'Ended' ? 0 : 1;
            $show->total_episodes = 0;
            $show->total_seasons = 0;
            $show->status = 0;

            // external ids
            $show->imdb_id = $tvMazeShow->externals['imdb'];
            $show->tvmaze_id = !empty($tvMazeShow->id) ? $tvMazeShow->id : null;
            $show->tvdb_id = !empty($tvMazeShow->externals['thetvdb']) ? (string)$tvMazeShow->externals['thetvdb'] : null;

            if (!$show->validate() || !$show->save()) {
                print_r($show->errors);
                return false;
            };
        }

        $show->tvmaze_updated_timestamp = $tvMazeShow->updated->timestamp ?? null;

        $external_id = !empty($show->imdb_id) ? 'IMDb: ' . $show->imdb_id : 'TVMAZE ID: ' . $show->tvmaze_id;
        Console::output('Doing TV Show: ' . $show->title . ' - ' . $external_id);

        if (empty($show->tvdb_id) && !empty($tvMazeShow->externals['thetvdb'])) {
            $show->tvdb_id = intval($tvMazeShow->externals['thetvdb'], 10);
        }

        if ($show->validate()) {
            $show->save();
        }

        $_showEpisodes = ShowsMetaQueue::find()
            ->where(['id_tvshow' => $show->id_tvshow])
            ->all();

        $showEpisodes = [];
        foreach ($_showEpisodes as $_show_episode) {
            if (empty($showEpisodes[$_show_episode->season])) {
                $showEpisodes[$_show_episode->season] = [];
            }
            $showEpisodes[$_show_episode->season][$_show_episode->episode] = $_show_episode;
        }

        $tvMazeEpisodes = $tvMaze->shows->getEpisodes($tvMazeShow->id);

        if (empty($tvMazeEpisodes)) {
            return false;
        }

        foreach ($tvMazeEpisodes as $itemEpisode) {
            if (
                empty($showEpisodes[$itemEpisode->season]) ||
                empty($showEpisodes[$itemEpisode->season][$itemEpisode->number])
            ) {
                // create new
                $showEpisode = new ShowsMetaQueue;
                $showEpisode->season = $itemEpisode->season;
                $showEpisode->episode = $itemEpisode->number;
                $showEpisode->title = $itemEpisode->name;
                $showEpisode->air_date = $itemEpisode->airdate;
                $showEpisode->id_tvshow = $show->id_tvshow;
                $showEpisode->state = env('EPISODES_QUEUE_WAITING_TORRENT', '4');
                $showEpisode->validate();
                $showEpisode->save();
            } else {
                if ($showEpisodes[$itemEpisode->season][$itemEpisode->number]->air_date !== $itemEpisode->airdate) {
                    $showEpisodes[$itemEpisode->season][$itemEpisode->number]->air_date = $itemEpisode->airdate;
                    if ($showEpisodes[$itemEpisode->season][$itemEpisode->number]->validate()) {
                        $showEpisodes[$itemEpisode->season][$itemEpisode->number]->save();
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param string $language
     *
     * @return string $lang
     */
    protected function getLangCodeByName(string $language)
    {
        $allLocales = \ResourceBundle::getLocales('');
        $foundLocales = [];
        foreach ($allLocales as $locale) {
            $currentName = \Locale::getDisplayLanguage($locale, 'en');
            if (strncmp($currentName, $language, strlen($currentName)) === 0) {
                $foundLocales[] = $locale;
            }
        }
        $lang = '';
        if (!empty($foundLocales[0])) {
            $lang = $foundLocales[0];
        }
        return $lang;
    }

    private function GET($url)
    {
        $client = new Client();
        $result = null;

        try {
            $response = $client->request('GET', $url);
            $result = json_decode($response->getBody());
        } catch (ClientException $e) {
            $result = null;
        } catch (ServerException $e) {
            $result = null;
        } catch (BadResponseException $e) {
            $result = null;
        } catch (\Exception $e) {
            $result = null;
        }

        return $result;
    }
}
