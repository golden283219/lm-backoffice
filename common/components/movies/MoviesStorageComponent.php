<?php
/**
 * Created by PhpStorm.
 * User: dev136
 * Date: 03.12.2019
 * Time: 18:46
 */

namespace common\components\movies;

use backend\models\site\Movies;
use common\models\MoviesStorage;
use common\models\site\MoviesSubtitles;
use common\helpers;

class MoviesStorageComponent extends \yii\base\Component
{

    public static function get_video_streams($id_movie)
    {
        $quality_codes = [
            '1' => 1080,
            '0' => 720,
            '4' => 480,
            '3' => 360
        ];

        $movie = Movies::find()->where(['id_movie' => $id_movie])->one();
        $movie_streams = MoviesStorage::find()->where(['id_movie' => $id_movie])->asArray()->all();

        $hotlink_protection_data = self::movies_hotlink_protection($movie->storage_slug);

        $streams_sorted = [];
        $streams = [];

        foreach ($movie_streams as $stream) {
            $streams_sorted[$quality_codes[$stream['quality']]] = $stream;
        }

        foreach ($streams_sorted as $quality => $stream_sorted) {
            $streams[$quality] = $hotlink_protection_data->hash . '/' . $hotlink_protection_data->expires . $movie->shard_url . $stream_sorted['url'];
        }

        return $streams;
    }

    public static function movies_hotlink_protection($storage_slug)
    {

        $client_ip = $_SERVER['REMOTE_ADDR'];

        $expires = time() + 6 * 3600;

        $hash = helpers\Generic::base64_md5_hash($storage_slug . $expires . $client_ip . 'G&59a*6dEL_8');

        return (object)[
            'hash' => $hash,
            'expires' => $expires
        ];

    }

    public static function get_movie_subtitles($id)
    {
        $subtitles = MoviesSubtitles::find()->where(['id_movie' => $id])->asArray()->all();

        $subtitles_formatted = [];
        foreach ($subtitles as $key => $value) {
            if (isset($value['shard']) && $value['shard'] !== '') {
                $subtitles_formatted[] = [
                    'id' => $value['id'],
                    'is_moderated' => $value['is_moderated'],
                    'language' => $value['language'],
                    'is_approved' => $value['is_approved'],
                    'url' => "{$value['url']}"
                ];
            } else {
                $url_parts = explode('/', $value['url']);
                $subtitles_formatted[] = [
                    'id' => $value['id'],
                    'is_moderated' => $value['is_moderated'],
                    'language' => $value['language'],
                    'is_approved' => $value['is_approved'],
                    'url' => "{$url_parts['0']}/{$url_parts['1']}/subtitles/{$url_parts['2']}.vtt"
                ];
            }

        }

        return $subtitles_formatted;
    }
}