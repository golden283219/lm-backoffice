<?php

namespace backend\models\site;

class MoviesModeration extends \common\models\site\MoviesModeration
{

  	public function getMovies()
    {
        return $this->hasOne(Movies::className(), ['id_movie' => 'id_movie']);
    }

}
