<?php

namespace common\helpers;

use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;

class Tmdb
{
    const TMDB_API_DOMAIN = 'https://api.themoviedb.org/3';

    /**
     * @param $tmdb_id
     * @param string $append_to_response
     * @param string $language
     *
     * @return mixed
     */
    public static function getMovie($tmdb_id, $append_to_response = '', $language = 'en-US')
    {
        $url = strtr('/movie/{id}?api_key={key}&language={lang}&append_to_response={append_to_response}', [
            '{id}' => $tmdb_id,
            '{key}' => env('TMDB_TOKEN'),
            '{lang}' => $language,
            '{append_to_response}' => $append_to_response,
        ]);

        try {
            $body = Json::decode(http_get_contents(self::TMDB_API_DOMAIN . $url));
        } catch (Exception $e) {
            Yii::error($e->getMessage(), 'TMDb:getMovie()');
        }

        if (empty($body)) {
            return null;
        }

        return $body;
    }

    /**
     * @param $id
     *
     * @param array $append
     *
     * @return null|array
     * @throws Exception
     */
    public static function getPersonDataByImdbId($id, $append = [])
    {
        $body = null;

        $url = '/find/' . $id . '?external_source=imdb_id';
        $url .= '&api_key=' . env('TMDB_TOKEN');

        try {
            $body = Json::decode(http_get_contents(self::TMDB_API_DOMAIN . $url));
        } catch (Exception $e) {}

        $id = ArrayHelper::getValue($body, 'person_results.0.id', null);

        if (!empty($id)) {
            return self::getPersonById($id, $append);
        }

        return null;
    }

    /**
     * @param $id
     * @param array $append
     *
     * @return mixed|null
     */
    public static function getPersonById($id, $append = [])
    {
        $body = null;

        $url = '/person/' . $id;
        $url .= '?append_to_response=' . implode(',', $append);
        $url .= '&api_key=' . env('TMDB_TOKEN');

        try {
            $body = Json::decode(http_get_contents(self::TMDB_API_DOMAIN . $url));
        } catch (Exception $e) {}

        return $body;
    }

    /**
     * Find Movie By Title And Year
     *
     * @param $title
     * @param $year
     *
     * @return null|array
     */
    public static function findMovieByTitleAndYear($title, $year)
    {
        $page = 1;
        $total_pages = null;

        $movie = null;
        while (is_null($total_pages) || ($page <= $total_pages && is_null($movie))) {
            $url = strtr('/search/movie?api_key={k}&page={p}&query={q}', [
                '{q}' => urlencode($title),
                '{k}' => env('TMDB_TOKEN'),
                '{p}' => $page,
            ]);

            try {
                $body = Json::decode(http_get_contents(self::TMDB_API_DOMAIN . $url));
            } catch (Exception $e) {
                Yii::error($e->getMessage(), 'TMDb:findMovieByTitleAndYear()');
            }

            if (empty($body)) {
                break;
            }

            foreach ($body['results'] as $result) {
                $release_date = !empty($result['release_date']) ? $result['release_date'] : '0000-00-00';
                $release_year = intval(date('Y',strtotime($release_date)), 10);
                if (
                    ($year == $release_year || $year == ($release_year - 1) || $year == ($release_year + 1)) &&
                    (strtolower($result['title']) == strtolower($title) || strtolower($result['original_title']) == strtolower($title))
                ) {
                    $movie = $result;
                    break;
                }
            }

            $total_pages = intval($body['total_pages'], 10);
            $page++;
        }

        return $movie;
    }

    /**
     * @param $tmdb_id
     * @param string $append_to_response
     * @param string $language
     *
     * @return array|null
     */
    public static function getTmdbTv($tmdb_id, $append_to_response = '', $language = 'en-US')
    {
        $body = null;

        $url = strtr('/tv/{id}?api_key={key}&language={lang}&append_to_response={append_to_response}', [
            '{id}' => $tmdb_id,
            '{key}' => env('TMDB_TOKEN'),
            '{lang}' => $language,
            '{append_to_response}' => $append_to_response,
        ]);

        try {
            $body = Json::decode(http_get_contents(self::TMDB_API_DOMAIN . $url));
        } catch (Exception $e) {}

        return $body;
    }

    /**
     * @param $id
     * @param string $external_source
     * @param string $language
     *
     * @return null|array
     */
    public static function findBy($id, $external_source = 'imdb_id', $language = 'en-US')
    {
        $url = strtr('/find/{id}?external_source={source}&api_key={key}&language={lang}', [
            '{id}'     => $id,
            '{source}' => $external_source,
            '{key}'    => env('TMDB_TOKEN'),
            '{lang}'   => $language
        ]);

        try {
            $body = Json::decode(http_get_contents(self::TMDB_API_DOMAIN . $url));
        } catch (Exception $e) {
            Yii::error($e->getMessage(), 'TMDb:findBy()');
        }

        if (empty($body)) {
            return null;
        }

        if (!empty($body['movie_results'])) {
            return $body['movie_results']['0'];
        }

        if (!empty($body['tv_results'])) {
            return $body['tv_results']['0'];
        }

        if (!empty($body['person_results'])) {
            return $body['person_results']['0'];
        }

        if (!empty($body['tv_episode_results'])) {
            return $body['tv_episode_results']['0'];
        }

        if (!empty($body['tv_season_results'])) {
            return $body['tv_season_results']['0'];
        }

        return null;
    }

    /**
     * @param $tmdbItem
     *
     * @return false|string
     */
    public static function getPosterContents($tmdbItem)
    {
        return http_get_contents('https://image.tmdb.org/t/p/original' . $tmdbItem['poster_path']);
    }

    /**
     * @param $tmdbItem
     *
     * @return false|string
     */
    public static function getBackdropContents($tmdbItem)
    {
        return http_get_contents('https://image.tmdb.org/t/p/original' . $tmdbItem['backdrop_path']);
    }

    /**
     * @param $type
     * @param $id
     *
     * @return string|null
     */
    public static function getTrailer($type, $id)
    {
        if (!in_array($type, ['movie', 'tv'])) {
            return null;
        }

        $url = "/$type/$id/videos?api_key=" . env('TMDB_TOKEN');

        try {
            $body = Json::decode(http_get_contents(self::TMDB_API_DOMAIN . $url));
        } catch (Exception $e) {
            Yii::error($e->getMessage(), 'TMDb:getTvTrailer()');
        }

        if (!empty($body) && !empty($body['results'])) {
            foreach ($body['results'] as $result) {
                if (strtolower($result['site']) === strtolower('YouTube')) {
                    return $result['key'];
                    break;
                }
            }
        }

        return null;
    }
}
