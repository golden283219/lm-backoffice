<?php

namespace api\modules\v1\resources\queue;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class MoviesSaveQueue extends \common\models\queue\MoviesSaveQueue implements Linkable
{
	public function fields()
	{
		return [
			'id', 'id_movie', 'slug', 'storage_slug', 'rel_title', 'worker_ip',
			'is1080p', 'is720p', 'is480p', 'is360p', 'is_dd', 'size_bytes',
			'os_hash', 'flag_quality', 'status', 'id_process', 'lang_iso_code'
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
    		Link::REL_SELF => Url::to(['movies-save-queue/view', 'id' => $this->id], true)
    	];
    }
}
