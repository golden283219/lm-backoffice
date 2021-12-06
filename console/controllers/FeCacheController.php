<?php

namespace console\controllers;

use common\models\ShowsEpisodes;
use common\models\site\Movies;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\ImdbCharts;

class FeCacheController extends Controller
{
    /**
     * How many latest episode
     * to put in cache
     * @var int
     */
    private $defaultEpisodesLimit = 30;

    /**
     * How many latest movies
     * to put in cache
     */
    private $defaultMoviesLimit = 15;

    /**
     * Update List of Movies Movies
     * that have to be pre-cached
     */
    public function actionUpdatePreCachedMovies()
    {
        // list of movies to put in cache
        $id_movies = [];

        $limit = intval(Yii::$app->keyStorage->get('fe.precache.movies.limit', $this->defaultMoviesLimit, false), 10);

        $config = new \Imdb\Config();
        $config->language = 'en, en-US';

        $imdbCharts = new ImdbCharts($config);

        $popular = $imdbCharts->getMostPopularChart();

        if (empty($popular)) {
            return Console::output('Fail: No Movies Found.');
        }

        foreach ($popular as $imdb_id) {
            $movie = Movies::find()->where(['imdb_id' => $imdb_id])->asArray()->one();

            if (!empty($movie)) {
                $id_movies[] = $movie['id_movie'];
            }

            if (count($id_movies) >= $limit) {
                break;
            }
        }

        $items = [];

        foreach ($id_movies as $id_movie) {
            $items[] = $id_movie;
            $items[] = '1';
        }

        // delete hash with cached episodes
        Yii::$app->redis->executeCommand('DEL', ['fe.precached.movies']);

        // save movies_ids in redis
        Yii::$app->redis->executeCommand('HMSET', array_merge(['fe.precached.movies'], $items));

        return Console::output('Success');
    }

    /**
     * Updates List of Episodes
     * that have to be pre-cached
     */
    public function actionUpdatePreCachedEpisodes()
    {
        $episodes = ShowsEpisodes::find()
            ->where(['is_active' => 1])
            ->orderBy(['air_date' => SORT_DESC])
            ->limit(Yii::$app->keyStorage->get('fe.precache.episodes.limit', $this->defaultEpisodesLimit, false))
            ->asArray()
            ->all();

        // delete hash with cached episodes
        Yii::$app->redis->executeCommand('DEL', ['fe.precached.episodes']);

        $items = [];
        foreach ($episodes as $episode) {
            $items[] = $episode['id'];
            $items[] = '1';
        }

        // set new cached episodes
        Yii::$app->redis->executeCommand('HMSET', array_merge(['fe.precached.episodes'], $items));

        Console::output('Done');
    }
}
