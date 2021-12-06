<?php

namespace api\modules\v1\resources;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class MoviesDownloadQueue extends \api\modules\v1\models\queue\Movies implements Linkable
{
    public function fields()
    {
        return [
            'id',
            'title',
            'year',
            'imdb_id',
            'is_downloaded',
            'bad_guids',
            'bad_titles',
            'flag_quality',
            'worker_ip',
            'original_language',
            'torrent_blob',
            'type',
            'priority'
        ];
    }

    /**
     * Returns a list of links.
     *
     * @return array the links
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['movies-download-queue/view', 'id' => $this->id], true)
        ];
    }
}
