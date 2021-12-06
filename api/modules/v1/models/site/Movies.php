<?php

namespace api\modules\v1\models\site;

use \common\models\site\Movies as MoviesSite;

class Movies extends MoviesSite
{
    public function getMoviesStorage()
    {
        return $this->hasMany(MoviesStorage::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesSubtitles()
    {
        return $this->hasMany(MoviesSubtitles::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesAudio()
    {
        return $this->hasMany(MoviesAudio::className(), ['id_movie' => 'id_movie']);
    }
}
