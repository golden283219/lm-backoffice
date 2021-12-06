<?php

namespace api\modules\v1\resources\queue;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class ShowsSaveQueue extends \api\modules\v1\models\queue\ShowsSaveQueue implements Linkable
{
	public function fields()
	{
		return [
			'id', 'id_tvshow', 'id_episode', 'episode', 'season', 'slug', 'is1080p', 'is_dd', 'id_process',
            'is720p', 'is480p', 'is360p', 'status', 'flag_quality', 'rel_title', 'remote_ip', 'original_language'
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
    		Link::REL_SELF => Url::to(['shows-save-queue/view', 'id' => $this->id], true)
    	];
    }
}
