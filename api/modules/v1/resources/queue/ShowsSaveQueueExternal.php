<?php

namespace api\modules\v1\resources\queue;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class ShowsSaveQueueExternal extends \api\modules\v1\models\queue\ShowsSaveQueueExternal implements Linkable
{
	public function fields()
	{
		return [
			'id',
            'id_tvshow',
            'id_episode',
            'episode',
            'season',
            'remote_ip',
            'files',
            'original_language',
            'rel_title',
            'slug',
            'status',
            'is_dd',
            'subs',
            'flag_quality',
            'id_process',
            'created_at',
            'updated_at',
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
    		Link::REL_SELF => Url::to(['shows-save-queue-external/view', 'id' => $this->id], true)
    	];
    }
}
