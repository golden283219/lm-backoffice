<?php

namespace console\jobs\metadata;

use common\components\imageStorage\ImageStorage;
use common\helpers\Tmdb;
use common\libs\Imdb\Title as ImdbTitle;
use common\models\MoviesRelated;
use common\models\site\MoviesCast;
use common\models\site\MoviesGenres;
use console\models\site\Movies;
use Throwable;
use Yii;

class MoviesMetaQueue extends AbstractMetaData
{
    const DOMAIN = 'https://www.imdb.com';

    public $imdbId;

    /**
     * @var ImdbTitle
     */
    private $ImdbTitle;

    public function execute($queue)
    {
        $this->ImageStorage = new ImageStorage();

        $imdb_id = $this->get_imdb_id();

        $movie = \common\models\site\Movies::find()->where(['imdb_id' => $imdb_id])->one();

        $this->ImdbTitle = $this->initImdbTitle($imdb_id);

        $IMDbCollectedData = $this->IMDbCollectTitleData($movie);

        $imdb_genres = $this->ImdbTitle->genres();

        $imdb_director = $this->ImdbTitle->director();
        $imdb_cast = $this->ImdbTitle->cast(true);
        $imdb_related = $this->ImdbTitle->getRelated();

        if (empty($IMDbCollectedData) || empty($movie)) {
            return false;
        }

        foreach ($IMDbCollectedData as $key => $value) {
            $movie->$key = $value;
        }

        yii2_ping_connection();
        if (!$movie->validate() || !$movie->save()) {
            Yii::error(json_encode($movie->errors, JSON_PRETTY_PRINT), 'MoviesMetaImdbQueue:movies->save()');
            return false;
        }

        $movie->has_metadata = 1;
        $movie->is_active = 1;
        $movie->cast = $this->saveMovieCast($imdb_cast, $imdb_director, $movie->id_movie);
        $movie->genres = $this->saveMovieGenres($imdb_genres, $movie->id_movie);

        $movie->save();

        $this->updateRelatedMovies($imdb_related, $movie->id_movie);

        return true;
    }

    /**
     * Save All Cast Information to Database
     *
     * @param array $cast
     * @param array $directors
     * @param $id_movie
     *d
     *
     * @return array
     */
    private function saveMovieCast(array $cast, array $directors, $id_movie)
    {
        $cast_cached = [];
        $db_cast = $this->saveCastInfo($cast, 'actor');
        $db_directors = $this->saveCastInfo($directors, 'director');
        MoviesCast::deleteAll(['id_movie' => $id_movie]);

        foreach ($db_cast as $db_cast_item) {
            $model = new MoviesCast();
            $model->id_movie = $id_movie;
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
     * @param $genres
     * @param $id_movie
     *
     * @return array
     */
    private function saveMovieGenres($genres, $id_movie)
    {
        $show_genres = [];
        $db_genres = \common\models\site\Genres::find()->asArray()->all();

        // delete all shows genres
        MoviesGenres::deleteAll(['id_movie' => $id_movie]);
        foreach ($db_genres as $db_genre) {
            if (in_array($db_genre['title'], $genres)) {
                $show_genres[] = [
                    'title' => $db_genre['title'],
                    'id' => $db_genre['id'],
                ];

                $model = new MoviesGenres();
                $model->id_movie = $id_movie;
                $model->id_genre = $db_genre['id'];
                $model->save();
            }
        }

        return $show_genres;
    }

    /**
     * @param $model
     *
     * @return array
     */
    private function IMDbCollectTitleData($model)
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

        $country = $this->ImdbTitle->country();
        $collectedData['country'] = !empty($country) && !empty($country['0']) ? $country['0'] : '';

        $collectedData['imdb_rating'] = $this->ImdbTitle->rating() ?? '';
        if (empty($collectedData['imdb_rating'])) {
            $collectedData['imdb_rating'] = 0;
        }
        $collectedData['duration'] = $this->ImdbTitle->runtime() ?? '';
        $collectedData['budget'] = $this->ImdbTitle->budget();
        $collectedData['tagline'] = $this->ImdbTitle->tagline();

        $plots = $this->ImdbTitle->plot_split();
        if (!empty($plots)) {
            $collectedData['description'] = $plots[0]['plot'];
        } else {
            $collectedData['description'] = $this->ImdbTitle->synopsis();
        }

        if (empty($model->poster)) {
            $imgPoster = http_get_contents($this->ImdbTitle->photo(false));
            $uploadInfo = $this->ImageStorage->handlePosterUpload($imgPoster);
            if ($uploadInfo['success'] == true && !empty($uploadInfo['path'])) {
                $collectedData['poster'] = '/' . $uploadInfo['path'];
            }
        }

        $site = $this->ImdbTitle->officialSites();
        if (!empty($site) && !empty($site['0']) && !empty($site[0]['url'])) {
            $collectedData['homepage'] = $site[0]['url'];
        }

        $tmdbMovie = Tmdb::findBy($this->imdbId, 'imdb_id', 'en-US');
        if (empty($tmdbMovie)) {
            $tmdbMovie = Tmdb::findMovieByTitleAndYear($collectedData['title'], $collectedData['year']);
        }

        if (!empty($tmdbMovie) && !empty($tmdbMovie['backdrop_path']) && empty($model->backdrop)) {
            $backdrop_contents = Tmdb::getBackdropContents($tmdbMovie);
            if (!empty($backdrop_contents)) {
                $collectedData['backdrop'] = $this->upload_backdrop($backdrop_contents);
            }
        }

        if (empty($collectedData['poster']) && !empty($tmdbMovie) && !empty($tmdbMovie['poster_path']) && empty($model->poster)) {
            $poster_contents = Tmdb::getPosterContents($tmdbMovie);
            if (!empty($poster_contents)) {
                $collectedData['poster'] = $this->upload_poster($poster_contents);
            }
        }

        if (!empty($tmdbMovie) && !empty($tmdbMovie['id'])) {
            $collectedData['youtube'] = Tmdb::getTrailer('movie', $tmdbMovie['id']);
        }

        return $collectedData;
    }

    /**
     * @return string
     */
    private function get_imdb_id()
    {
        return substr($this->imdbId, 0, 2) === 'tt' ? substr($this->imdbId, 2) : $this->imdbId;
    }

    /**
     * @param $title
     * @param $year
     *
     * @return array|null
     */
    private function find_tmdb_movie_by_title_and_year($title, $year)
    {
        $tmdb_search_api = $this->tmdbClient->getSearchApi();
        $search_results = $tmdb_search_api->searchMovies($title);
        foreach ($search_results['results'] as $search_result) {
            if (empty($search_result['release_date'])) continue;
            $movie_year = explode('-', $search_result['release_date'])['0'];
            if ((strtolower($search_result['title']) === strtolower($title) || strtolower($search_result['original_title']) === strtolower($title)) && $year == $movie_year) {
                return $search_result;
            }
        }
        return null;
    }

    /**
     * @param $related_movies_imdb
     * @param $id_movie
     *
     * @throws Throwable
     */
    private function updateRelatedMovies($related_movies_imdb, $id_movie)
    {
        // Delete old relations
        MoviesRelated::deleteAll('id_movie = ' . $id_movie);

        foreach ($related_movies_imdb as $related_movie_imdb) {
            $this->insertRelatedMovie($id_movie, $related_movie_imdb);
        }
    }

    /**
     * Insert Related Movie
     *
     * @param $id_movie
     * @param $imdb_id
     *
     * @throws Throwable
     */
    private function insertRelatedMovie($id_movie, $imdb_id)
    {
        $movie = Movies::findOne(['imdb_id' => sanitize_imdb_id($imdb_id)]);

        if ($movie) {
            // Add relation
            $relatedMovie = new MoviesRelated();
            $relatedMovie->related_id_movie = $movie->id_movie;
            $relatedMovie->id_movie = $id_movie;
            $relatedMovie->save();
        }
    }
}


