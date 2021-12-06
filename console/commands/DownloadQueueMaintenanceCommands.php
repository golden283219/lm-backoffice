<?php


namespace console\commands;

use common\models\queue\Shows;
use console\jobs\downloadQueue\UpdateIMDbId;
use console\jobs\metadata\ShowsMetaImdbQueue;
use console\models\tvmazeQueue\AddShows;
use console\models\tvmazeQueue\UpdateShowsEpisodes;
use TVMaze\API\Client as TVMaze;
use Yii;
use yii\helpers\Console;
use yii\queue\amqp\Command;

class DownloadQueueMaintenanceCommands extends Command
{
    /**
     * @const int TVMAZE Per Page Items
     */
    const TVMAZE_PER_PAGE = 250;

    /**
     * Runs Adding new English tv show
     *
     * @param null $tvmaze_id
     *
     * @return int
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function actionTvmazeAddShows($tvmaze_id = null)
    {
        if (!empty($tvmaze_id)) {
            Yii::$app->downloadsListUpdateQueue->push(
                new AddShows([
                    'tvmaze_id' => $tvmaze_id
                ])
            );

            Console::output('Adding TV Show Finished: ' . $tvmaze_id);

            return self::EXIT_CODE_NORMAL;
        }

        $tvMaze = new TVMaze();

        $id_pointer = Yii::$app->keyStorage->get('tvmaze.id_pointer', 1);

        $page = floor($id_pointer / self::TVMAZE_PER_PAGE);
        do {
            $tvMazeShows = null;
            try {
                $tvMazeShows = $tvMaze->shows->getAll($page);
            } catch (\Exception $e) {}

            if (empty($tvMazeShows)) {
                break;
            }

            foreach ($tvMazeShows as $tvMazeShow) {
                if (strtolower($tvMazeShow->language) !== 'english') continue;
                Yii::$app->downloadsListUpdateQueue->push(
                    new AddShows([
                        'tvmaze_id' => $tvMazeShow->id
                    ])
                );
                Yii::$app->keyStorage->set('tvmaze.id_pointer', $tvMazeShow->id);
            }

            $page++;
            Console::output('$page='.$page);
        } while (!empty($tvMazeShows));

        Console::output('Done.');

        return self::EXIT_CODE_NORMAL;
    }

    /**
     * Tries To Find IMDbID for given show, if none given will update all shows.
     *
     * @param null $id_show
     *
     * @return bool
     */
    public function actionUpdateDownloadQueueImdbId($id_show = null)
    {
        if (!empty($id_show)) {
            $show = Shows::find()->where(['id_tvshow' => $id_show])->one();

            if (empty($show)) {
                Console::output('Unable to find show: `' . $id_show . '`');
                return false;
            }

            Yii::$app->downloadsListUpdateQueue->push(new UpdateIMDbId([
                'id_tvshow' => $id_show
            ]));

            return true;
        }

        foreach (Shows::find()->where(['imdb_id' => ''])->orWhere(['imdb_id' => null])->batch(500) as $showsBatch) {
            foreach ($showsBatch as $show) {
                Yii::$app->downloadsListUpdateQueue->push(new UpdateIMDbId([
                    'id_tvshow' => $show->id_tvshow
                ]));
            }
        }
    }

    /**
     * Run Update All existing shows episodes
     * with air dates and adding missing episodes
     *
     * @param array $params
     *
     * @return int
     */
    public function actionTvmazeUpdateShowsEpisodes(array $params = [])
    {
        $config = parse_console_array($params);

        if (!empty($config['imdb_id']) || !empty($config['tvmaze_id']) || !empty($config['tvdb_id'])) {
            Yii::$app->downloadsListUpdateQueue->push(new UpdateShowsEpisodes([
                'config' => $config
            ]));

            return 0;
        }

        foreach (Shows::find()->batch(100) as $shows) {
            foreach ($shows as $show) {
                $config = [];

                if (!empty($show->imdb_id)) {
                    $config['imdb_id'] = $show->imdb_id;
                }

                if (!empty($show->tvmaze_id)) {
                    $config['tvmaze_id'] = $show->tvmaze_id;
                }

                if (!empty($show->tvdb_id)) {
                    $config['tvdb_id'] = $show->tvdb_id;
                }

                Yii::$app->downloadsListUpdateQueue->push(new UpdateShowsEpisodes([
                    'config' => $config
                ]));
            }
        }

        return 0;
    }

    /**
     * Checks existing shows last update timestamp,
     * if its older compare to tvmaze, runs update for that show
     */
    public function actionUpdateShowsByTimestamp()
    {
        $tvmaze_updates = json_decode(file_get_contents('https://api.tvmaze.com/updates/shows'));

        $_shows = Shows::find()->asArray()->all();

        $shows = [];
        foreach ($_shows as $_show) {
            if (!empty($_show['tvmaze_id'])) {
                $shows[$_show['tvmaze_id']] = $_show['tvmaze_updated_timestamp'];
            }
        }

        foreach ($tvmaze_updates as $tvmaze_id => $tvmaze_updated_timestamp) {
            if (empty($shows[$tvmaze_id])) {
                continue;
            }

            if (is_null($shows[$tvmaze_id]) || $shows[$tvmaze_id] < $tvmaze_updated_timestamp) {
                $this->stdout(sprintf("✔ Added to update queue: %s", $tvmaze_id) . PHP_EOL, Console::BOLD, Console::FG_GREEN);
                Yii::$app->downloadsListUpdateQueue->push(new UpdateShowsEpisodes([
                    'config' => ['tvmaze_id' => $tvmaze_id]
                ]));

                // Run site metadata update for updated show on tvmaze
                $queue_show = \console\models\queue\Shows::find()->where(['tvmaze_id' => $tvmaze_id])->one();
                if (!empty($queue_show)) {
                    $site_show = \console\models\site\Shows::find()->where(['id_show' => $queue_show->id_tvshow]);

                    if (!empty($site_show) && !empty($site_show->imdb_id)) {
                        Yii::$app->metadataDownloadQueue->push(new ShowsMetaImdbQueue([
                            'imdbId' => $site_show->imdb_id,
                        ]));
                    }
                }
            }
        }
    }
}
