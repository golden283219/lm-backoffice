<?php

namespace console\controllers;

use common\models\ImdbCharts;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use common\models\Movies;
use common\helpers\SimpleHtmlDom;
use common\models\site\MoviesFeatured;

class FeaturedMoviesController extends Controller
{
    private $added = 0;
    private $api_path = 'https://api.trakt.tv';

    private function getTrendingMovies(){
        $ch = curl_init();

        // $filter = 'years='  . (date('Y') - 1) . '-' . date('Y'); // Years Range
        $filter = 'years=' . date('Y');
        $url = $this->api_path . "/movies/trending?" . $filter;

        $this->stdout("URL: " . $url .  PHP_EOL . PHP_EOL, Console::BOLD, Console::FG_BLUE);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "trakt-api-version: 2",
            "trakt-api-key: " . Yii::$app->params['traktApiKey']
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function actionUpdate(){
        // Get Trending Movies
        $trending_movies = $this->getTrendingMovies();

        // Remove Featured Movies
        MoviesFeatured::deleteAll();

        foreach($trending_movies as $trending_movie){
            $imdb_id = $trending_movie['movie']['ids']['imdb'];
            $imdb_id = substr($imdb_id, 2);

            $this->stdout($trending_movie['movie']['year'] . " - \"" . $trending_movie['movie']['title'] . "\" ", Console::BOLD, Console::FG_YELLOW);

            $movie = Movies::findOne(['imdb_id' => $imdb_id]);

            // If movie exist
            if($movie){
                $moviesFeatured = new MoviesFeatured();
                $moviesFeatured->id_movie = $movie->id_movie;
                $moviesFeatured->date_added = date("Y-m-d H:i:s");
                if($moviesFeatured->insert()){
                    $this->added++;
                }

                $this->stdout("✔" . PHP_EOL, Console::BOLD, Console::FG_GREEN);
            } else {
                $this->stdout("x" . PHP_EOL, Console::BOLD, Console::FG_RED);
            }

        }

        $this->stdout(PHP_EOL . "Added " . $this->added . " Movies\n", Console::BOLD);
        $this->stdout("Done\n", Console::FG_GREEN);
    }

    public function actionUpdateOldWebsite()
    {
        // list of movies to put in trending
        $id_movies = [];
        $limit = 16;

        $config = new \Imdb\Config();
        $config->language = 'en, en-US';

        $imdbCharts = new ImdbCharts($config);

        $popular = $imdbCharts->getMostPopularChart();

        if (empty($popular)) {
            return Console::output('Fail: No Movies Found.');
        }

        foreach ($popular as $imdb_id) {
            $movie = \common\models\site\Movies::find()->where(['imdb_id' => $imdb_id])->asArray()->one();

            if (!empty($movie)) {
                $id_movies[] = $movie['id_movie'];
            }

            if (count($id_movies) >= $limit) {
                break;
            }
        }

        if (count($id_movies) === 0) {
            return Console::output('Fail: No Movies Found on site.');
        }

        MoviesFeatured::deleteAll();

        foreach ($id_movies as $key => $id_movie) {
            $date_added = date('Y-m-d H:i:s', strtotime("-$key hours"));

            $movies_featured = new MoviesFeatured();
            $movies_featured->id_movie = $id_movie;
            $movies_featured->date_added = $date_added;

            $movies_featured->save();
        }
    }
}
