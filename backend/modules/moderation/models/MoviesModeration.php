<?php

namespace backend\modules\moderation\models;

use Yii;
use \backend\models\site\Movies;

class MoviesModeration extends \common\models\site\MoviesModeration
{
    public function getMovies()
    {
        return $this->hasOne(Movies::className(), ['id_movie' => 'id_movie']);
    }
}
