<?php


namespace console\commands;

use console\jobs\metadata\MoviesMetaQueue;
use console\jobs\metadata\ShowsMetaImdbQueue;
use console\jobs\metadata\UpdateTitles;
use console\models\site\Movies;
use yii\queue\amqp\Command;
use console\models\site\Shows;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class MetadataDownloadQueueCommand extends Command
{
    public function actionScrapeMoviesMeta($command = null)
    {
        if ($command === 'all') {
            foreach (Movies::find()->batch(250) as $movies) {
                foreach ($movies as $movie) {
                    if (!empty($movie->imdb_id)) {
                        Yii::$app->metadataDownloadQueue->push(new MoviesMetaQueue([
                            'imdbId' => $movie->imdb_id,
                        ]));

                        Console::output('Added: tt' . $movie->imdb_id);
                    }
                }
            }

            return true;
        }

        $imdb_id = extract_imdb_id($command);

        if (!empty($imdb_id)) {
            $imdb_id = str_replace('tt', '', $imdb_id);
            $movie = Movies::find()
                ->where(['imdb_id' => $imdb_id])
                ->one();

            if (!empty($movie)) {
                $job = new MoviesMetaQueue([
                    'imdbId' => $imdb_id,
                ]);

                Yii::$app->metadataDownloadQueue->push($job);
                return Console::output('Added:');
            }

            return Console::output('Not Added:');
        }

        $commands = array_map(function ($item) {
            return trim($item);
        }, explode(',', $command));

        $movies_without_meta = [];
        $movies_without_poster = [];
        $movies_without_cast = [];

        if (in_array('no-meta', $commands)) {
            $movies_without_meta = Movies::find()
                ->where(['<>', 'has_metadata', 1])
                ->orderBy(['date_added' => SORT_DESC])
                ->asArray()
                ->all();
        }

        if (in_array('no-poster', $commands)) {
            $movies_without_poster = $this->findMoviesWithoutPoster();
        }

        if (in_array('no-cast', $commands)) {
            $movies_without_cast = $this->findMoviesWithoutCast();
        }

        $movies = array_merge($movies_without_cast, $movies_without_meta, $movies_without_poster);

        $_movies = [];
        foreach ($movies as $movie) {
            $_movies[$movie['imdb_id']] = $movie;
        }

        $movies = $_movies;

        foreach ($movies as $movie) {
            try {
                $imdb_id = str_replace('tt', '', $movie['imdb_id']);
            } catch (\Exception $e) {
                Console::error($e->getMessage());
            }

            if (!empty($imdb_id)) {
                $job = new MoviesMetaQueue([
                    'imdbId' => $imdb_id,
                ]);

                Yii::$app->metadataDownloadQueue->push($job);
            }
        }

        return Console::output('Added:');
    }

    public function actionUpdateTitles($type = 'shows,movies')
    {
        $type = explode(',', $type);
        $type = array_map(function ($item) {
            return trim($item);
        }, $type);

        foreach ($type as $item) {
            switch ($item) {
                case 'movies':
                    $movies = Movies::find()->asArray()->all();
                    foreach ($movies as $movie) {
                        Yii::$app->metadataDownloadQueue->push(new UpdateTitles([
                            'type'    => 'movies',
                            'imdb_id' => 'tt' . $movie['imdb_id']
                        ]));
                    }
                    break;
                case 'shows':
                    $shows = Shows::find()->asArray()->all();
                    foreach ($shows as $show) {
                        Yii::$app->metadataDownloadQueue->push(new UpdateTitles([
                            'type'     => 'shows',
                            'imdb_id' => $show['imdb_id']
                        ]));
                    }
                    break;
            }
        }

        Console::output('Done');
    }

    /**
     * Find Movies Without Cast
     */
    public function findMoviesWithoutCast()
    {
        $movies_without_cast = [];

        $query = (new \yii\db\Query())
            ->select([
                'id_movie', 
                'cast_count' => 'count(id)'
            ])
            ->from('movies_cast')
            ->groupBy(['id_movie']);

        $all_movies_from_cast = $query->all();

        foreach ($all_movies_from_cast as $movie_from_cast) {
            $cast_count = intval($movie_from_cast['cast_count'], 10);

            if ($cast_count < 5) {
                $movie = (new \yii\db\Query())
                    ->select(['imdb_id', 'id_movie'])
                    ->from('movies')
                    ->where(['id_movie' => $movie_from_cast['id_movie']])
                    ->one();
                
                $imdb_id = ArrayHelper::getValue($movie, 'imdb_id');

                if (!empty($imdb_id)) {
                    $movies_without_cast[] = $movie;
                }
            }
        }

        return $movies_without_cast;
    }

    /**
     * Finds Movies Without Poster
     */
    public function findMoviesWithoutPoster()
    {
        return Movies::find()
            ->where(['poster' => null])
            ->orWhere(['poster' => ''])
            ->orderBy(['date_added' => SORT_DESC])
            ->asArray()
            ->all();
    }

    /**
     * Put in download queue all episodes
     * without metadata
     *
     * @param null $command
     *
     * @return bool|int
     */
    public function actionScrapeEpisodesMeta($command = null)
    {
        if ($command === 'all') {
            foreach (Shows::find()->batch(250) as $shows) {
                foreach ($shows as $show) {
                    if (!empty($show->imdb_id)) {
                        Yii::$app->metadataDownloadQueue->push(new ShowsMetaImdbQueue([
                            'imdbId' => $show->imdb_id,
                        ]));

                        Console::output('Added: ' . $show->imdb_id);
                    }
                }
            }

            return true;
        }

        $imdb_id = extract_imdb_id($command);

        if (!empty($imdb_id)) {
            $imdb_id = str_replace('tt', '', $imdb_id);
            $tv_show = Shows::find()
                ->where(['imdb_id' => 'tt' . $imdb_id])
                ->one();

            if (!empty($tv_show)) {
                $job = new ShowsMetaImdbQueue([
                    'imdbId' => $tv_show['imdb_id'],
                ]);

                Yii::$app->metadataDownloadQueue->push($job);

                return Console::output('Added:');
            }

            return Console::output('Not Added:');
        }

        $tv_shows = [];
        $tv_shows = array_merge($tv_shows, Shows::getShowsWithoutMeta());
        $tv_shows = array_merge($tv_shows, Shows::getShowsEpisodesWithoutMeta());
        $tv_shows = array_merge($tv_shows, Shows::getShowsEpisodesWithoutAirDate());
        $tv_shows = array_merge($tv_shows, static::getShowsWithoutCast());

        $tv_shows_ = [];
        foreach ($tv_shows as $tv_show) {
            if (!empty($tv_show['imdb_id']) && empty($tv_shows_[$tv_show['imdb_id']])) {
                $tv_shows_[$tv_show['imdb_id']] = $tv_show;
            }
        }

        $tv_shows = $tv_shows_;

        foreach ($tv_shows as $tv_show) {
            $job = new ShowsMetaImdbQueue([
                'imdbId' => $tv_show['imdb_id'],
            ]);

            Yii::$app->metadataDownloadQueue->push($job);
        }
    }

    private static function getShowsWithoutCast()
    {
        $shows_without_cast = [];

        $query = (new \yii\db\Query())
            ->select([
                'id_show', 
                'cast_count' => 'count(id)'
            ])
            ->from('shows_cast')
            ->groupBy(['id_show']);

        $all_shows_from_cast = $query->all();

        foreach ($all_shows_from_cast as $show_from_cast) {
            $cast_count = intval($show_from_cast['cast_count'], 10);

            if ($cast_count < 5) {
                $show = (new \yii\db\Query())
                    ->select(['imdb_id', 'id_show'])
                    ->from('shows')
                    ->where(['id_show' => $show_from_cast['id_show']])
                    ->one();
                
                $imdb_id = ArrayHelper::getValue($show, 'imdb_id');

                if (!empty($imdb_id)) {
                    $shows_without_cast[] = $show;
                }
            }
        }

        // foreach (Shows::find()->batch(250) as $shows) {
        //     foreach ($shows as $show) {
        //         if (!empty($show->imdb_id)) {
        //             $cast_count = $show->castCount();
        //             if ($cast_count < 5) {
        //                 $shows_without_cast[] = [
        //                     'id_show' => $show->id_show,
        //                     'imdb_id'  => $show->imdb_id
        //                 ];
        //             }
        //         }
        //     }
        // }

        return $shows_without_cast;
    }
}
