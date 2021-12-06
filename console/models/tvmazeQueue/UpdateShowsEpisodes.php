<?php


namespace console\models\tvmazeQueue;

use yii\helpers\Console;

class UpdateShowsEpisodes extends AbstractTvMazeQueue
{
    /**
     * @var array
     */
    public $config = [];
 
    public function execute($queue)
    {
        // If We Specified $external_id do single show
        if (!empty($this->config['tvmaze_id']) || !empty($config['imdb_id']) || !empty($config['tvdb_id'])) {
            $result = $this->UpdateShow($this->config) ? 'success' : 'fail';
            Console::output('Result: ' . $result . PHP_EOL);
            return true;
        }

        return Console::output('Result: fail. Reason: No Specified external_id.');
    }
}
