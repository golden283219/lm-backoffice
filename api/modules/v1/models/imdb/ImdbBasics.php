<?php


namespace api\modules\v1\models\imdb;

use common\helpers\SimpleHtmlDom;

class ImdbBasics extends \common\models\imdb\ImdbBasics
{
    public function getAkas()
    {
        return $this->hasMany(ImdbAkas::className(), ['titleId' => 'tconst']);
    }

    public function getRatings()
    {
        return $this->hasMany(ImdbRatings::className(), ['tconst' => 'tconst']);
    }

    /**
     * @param $imdb_id
     * @return string|null
     */
    public static function getOriginalLanguage($imdb_id)
    {
        $page_contents = file_get_contents('https://www.imdb.com/title/' . $imdb_id);

        $original_language = imdb_find_original_language($page_contents);
        $original_language = !empty($original_language) ? $original_language : 'en';

        return $original_language;
    }
}