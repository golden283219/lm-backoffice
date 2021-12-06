<?php

namespace api\modules\v1\resources\queue;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class MoviesSaveQueueExternal extends \api\modules\v1\models\queue\MoviesSaveQueueExternal implements Linkable
{
	public function fields()
	{
		return [
			'id',
            'id_movie',
            'worker_ip',
            'files',
            'rel_title',
            'slug',
            'storage_slug',
            'status',
            'is_dd',
            'flag_quality',
            'size_bytes',
            'subs',
            'os_hash',
            'id_process',
            'lang_iso_code',
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
    		Link::REL_SELF => Url::to(['movies-save-queue-external/view', 'id' => $this->id], true)
    	];
    }
}
