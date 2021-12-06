<?php

namespace backend\models\queue;

use common\models\queue\Movies as MoviesQueue;

class Movies extends MoviesQueue
{
	const STATUS_DOWNLOADED = 1;
  	const STATUS_WAITING_TORRENT_DOWNLOADER= 13;
  	const STATUS_MISSING_DOWNLOAD_CANDIDATE = 14;
  	const STATUS_CONVERSION_IN_PROGRESS = 2;
  	const STATUS_WAITING_USENET_DOWNLOADER = 10;

  	public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_INSERT, 'handle_init_movie_moderation_history');
        $this->on(self::EVENT_BEFORE_UPDATE, 'handle_init_movie_moderation_history');

    }

    public function detachEvents()
    {
        $this->off(self::EVENT_AFTER_INSERT, 'handle_init_movie_moderation_history');
        $this->off(self::EVENT_BEFORE_UPDATE, 'handle_init_movie_moderation_history');
    }
}
