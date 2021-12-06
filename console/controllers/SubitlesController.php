<?php

namespace console\controllers;

use console\models\queue\MovieQueue;
use console\models\queue\ShowsQueue;
use yii\console\Controller;

class SubitlesController extends  Controller
{
    /**
     * @param string $imdbId
     * @param string $title
     * @param int $movieYear
     */
    public function actionPopulateMovie(string $imdbId = '0758746', string $title = 'Friday the 13th', int $movieYear = 2009) : void
    {
        $job = new MovieQueue([
            'imdbId' => $imdbId,
            'movieTitle' => $title,
            'movieYear' => $movieYear
        ]);
        \Yii::$app->redisMoviesQueue->push($job);
    }

    /**
     * @param string $imdbId
     * @param string $title
     * @param int $season
     * @param int $episode
     */
    public function actionPopulateShow(string $imdbId = "tt1520211", string $title = '', int $season = 1, int $episode = 1) : void
    {
        $job = new ShowsQueue([
            'imdbId' => $imdbId,
            'episodeTitle' => $title,
            'episode' => $episode,
            'season' => $season,
        ]);

        \Yii::$app->redisShowsQueue->push($job);
    }
}
