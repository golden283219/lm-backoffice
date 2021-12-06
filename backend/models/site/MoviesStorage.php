<?php

namespace backend\models\site;

use common\models\site\MoviesStorage as MoviesStorageSite;

class MoviesStorage extends MoviesStorageSite
{
    /**
     * Extracts URL from Storage PATH
     *
     * @param $url
     *
     * @return string|null
     */
    public static function extractURLFromStoragePath($url)
    {
        $re = '/\/(stor[0-9])\/(movies)\/(.+)\/([0-9]+p)\/([a-z0-9]+\.m3u8)/m';

        preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);

        if (empty($matches['0'])) {
            return null;
        }

        return $matches[0]['2'].'/'.$matches[0]['3'].'/'.$matches[0]['4'].'/'.$matches[0]['5'];
    }

    public function getMovie()
    {
        return $this->hasOne(Movies::className(), ['id_movie' => 'id_movie']);
    }
}
