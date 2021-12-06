<?php


namespace console\models\tvmazeQueue;

use console\models\queue\Shows as ShowsQueue;
use TVMaze\API\Client as TVMaze;
use yii\console\ExitCode;
use yii\helpers\Console;

class AddShows extends AbstractTvMazeQueue
{
    /**
     * TV Maze ID
     * @var int
     */
    public $tvmaze_id;

    public function execute($queue)
    {
        Console::output('Doing: #' . $this->tvmaze_id);

        $show = ShowsQueue::find()->where(['tvmaze_id' => $this->tvmaze_id])->one();
        if (!empty($show)) {
            Console::output('Success: false, tv show already exists.');
            return false;
        }

        $tvmaze = new TVMaze();
        $tvmazeShow = $tvmaze->shows->getById($this->tvmaze_id);

        $show                    = new ShowsQueue();
        $show->tvmaze_id         = $this->tvmaze_id;

        if (!empty($tvmazeShow->externals) && !empty($tvmazeShow->externals['thetvdb'])) {
            $show->tvdb_id = $tvmazeShow->externals['thetvdb'];
        }

        if (!empty($tvmazeShow->externals) && !empty($tvmazeShow->externals['imdb'])) {
            $show->imdb_id = $tvmazeShow->externals['imdb'];
        }

        $locale = getLocaleByDisplayName($tvmazeShow->language);
        $show->original_language = !empty($locale) ? $locale['0'] : 'en';

        $show->title = $tvmazeShow->name;
        $show->total_seasons = 0;
        $show->total_episodes = 0;
        $show->in_production = $tvmazeShow->status === 'Ended' ? 0 : 1;
        $show->episode_duration = 0;
        $show->tvmaze_updated_timestamp = $tvmazeShow->updated->timestamp;
        $show->first_air_date = !empty($tvmazeShow->premiered) ? date('Y-m-d', $tvmazeShow->premiered->timestamp) : '2017-07-12';
        if ($show->validate() && $show->save()) {
            $this->UpdateShow([
                'tvmaze_id' => $this->tvmaze_id
            ]);

            Console::output('Success.');
            return ExitCode::OK;
        }

        Console::output('Fail. Unable to Save TV Show');

        print_r($show->errors);

        return ExitCode::UNSPECIFIED_ERROR;
    }
}
