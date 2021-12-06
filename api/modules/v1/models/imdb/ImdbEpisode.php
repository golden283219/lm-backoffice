<?php


namespace api\modules\v1\models\imdb;


class ImdbEpisode extends \common\models\imdb\ImdbEpisode
{
    public function getBasics()
    {
        return $this->hasOne(ImdbBasics::className(), ['tconst' => 'tconst']);
    }
}