<?php

namespace api\modules\v1\resources\queue;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class ShowsDownloadQueue extends \backend\models\queue\ShowsMeta implements Linkable
{
    public function fields()
    {
        return [
            'id_meta', 'id_tvshow', 'season', 'episode', 'title', 'air_date', 'rel_title',
            'bad_guids', 'bad_titles', 'state', 'priority', 'torrent_blob', 'type'
        ];
    }

    public function extraFields()
    {
        return [
            'showMetadata' => function ($model) {
                return $model->show;
            }
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
            Link::REL_SELF => Url::to(['shows-download-queue/view', 'id' => $this->id_meta], true)
        ];
    }
}
