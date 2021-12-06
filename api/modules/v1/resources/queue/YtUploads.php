<?php

namespace api\modules\v1\resources\queue;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class YtUploads extends \api\modules\v1\models\queue\YtUploads implements Linkable
{
	public function fields()
	{
		return [
            'key',
            'yt_link'
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
    		Link::REL_SELF => Url::to(['yt-uploads/view', 'id' => $this->key], true)
    	];
    }
}
