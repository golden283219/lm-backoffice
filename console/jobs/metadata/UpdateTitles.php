<?php


namespace console\jobs\metadata;

use common\libs\Imdb\Person;
use common\models\site\Movies;
use common\models\site\Shows;

class UpdateTitles extends AbstractMetaData
{
    public $imdb_id;

    /**
     * @var Person
     */
    public $type;

    public function execute($queue)
    {

        print_r(['type' => $this->type, 'imdb_id' => $this->imdb_id]);

        if (empty($this->imdb_id) || empty($this->type)) {
            return false;
        }

        switch ($this->type) {
            case 'movies':
                $this->updateMovieTitle();
                break;
            case 'shows':
                $this->updateShowTitle();
                break;
        }
    }

    private function updateMovieTitle()
    {
        $imdb_id = sanitize_imdb_id($this->imdb_id);
        $title = $this->getIMDbTitle($this->imdb_id);

        if (empty($title)) {
            return false;
        }

        Movies::updateAll(['title' => $title], "imdb_id = '{$imdb_id}'");
    }

    private function updateShowTitle()
    {
        $title = $this->getIMDbTitle($this->imdb_id);

        if (empty($title)) {
            return false;
        }

        Shows::updateAll(['title' => $title], "imdb_id = '{$this->imdb_id}'");
    }
}
