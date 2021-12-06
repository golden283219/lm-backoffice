<?php

namespace console\jobs\metadata;

use common\libs\Imdb\Config as ImdbConfig;
use common\libs\Imdb\Title as ImdbTitle;
use common\models\imdb\ImdbBasics;
use console\models\imdb\ImdbAkas;
use Yii;
use yii\base\BaseObject;
use common\models\queue\CastImdb;
use common\models\queue\KnownForShows;
use common\models\queue\KnownForMovies;
use common\components\ImageStorage\ImageStorage;
use common\helpers\Tmdb;
use common\models\Movies;
use common\models\Shows;
use common\helpers\SimpleHtmlDom;
use yii\helpers\ArrayHelper;

abstract class AbstractMetaData extends BaseObject implements \yii\queue\JobInterface
{
    public $injectedProxy;
    protected $ImageStorage;

    /**
     * Save Cast Into Database Or If
     * We don't have it
     *
     * @param array $cast
     *
     * @param string $role
     *
     * @return array
     */
    protected function saveCastInfo(array $cast, $role = 'actor') : array
    {
        $db_cast = [];

        foreach ($cast as $cast_item) {
            $isNewRecord = false;

            // insert into database or fetch
            $db_cast_item = CastImdb::find()
                ->where(['imdb_actor_id' => $cast_item['imdb']])
                ->one();

            if (null === $db_cast_item) {
                $isNewRecord = true;

                $db_cast_item = new CastImdb;
                $db_cast_item->imdb_actor_id = $cast_item['imdb'];
                $db_cast_item->full_name = $cast_item['name'];
                $db_cast_item->slug = slugify($cast_item['imdb'] . '-' . $cast_item['name']);
                $db_cast_item->save();
            }

            if (!empty($db_cast_item->id)) {
                $db_cast[] = [
                    'id'    => $db_cast_item->id,
                    'hero'  => $cast_item['role'],
                    'name'  => $cast_item['name'],
                    'role'  => $role,
                    'picture_url' => !empty($db_cast_item['photo']) ? $db_cast_item['photo'] : ''
                ];
            }

            if ($isNewRecord || env('CAST_METADATA_DOWNLOAD', 'on-demand') === 'force') {
                Yii::$app->castMetadataDownloadQueue->push(new CastImdbMetadataJob([
                    'castImdbId' => $cast_item['imdb'],
                ]));
            }
        }

        return $db_cast;
    }

    /**
     * Gets IMDb Title From Local Database
     *
     * @param $imdb_id
     * @return mixed|null
     */
    protected function getIMDbTitleDeprecated($imdb_id)
    {
        $akas = ImdbAkas::find()->where(['titleId' => $imdb_id])->asArray()->all();

        // filter working ty
        $f_akas = array_filter($akas, function ($item) {
            return $item['types'] !== 'working';
        });

        $f_akas = array_filter($f_akas, function ($item) {
            return (in_array($item['region'], ['US', 'CA', 'AU', 'GB']) && ($item['language'] === 'en'));
        });

        if (empty($f_akas)) {
            $f_akas = array_filter($akas, function ($item) {
                return $item['language'] === 'en';
            });
        }

        usort($f_akas, function ($a, $b) {
            return intval($a['ordering']) - intval($b['ordering']);
        });

        if (empty($f_akas)) {
            return null;
        }

        return $f_akas[0]['title'];
    }

    /**
     * @param $imdb_id
     * @return mixed|null
     */
    protected function getIMDbTitle($imdb_id)
    {
        $imdb_id_sanitized = 'tt' . sanitize_imdb_id($imdb_id);
        $imdb_basics = ImdbBasics::find()->where(['tconst' => $imdb_id_sanitized])->asArray()->one();

        if (empty($imdb_basics)) {
            return null;
        }

        return $imdb_basics['primary_title'] ?? $imdb_basics['original_title'];
    }

    /**
     * Get Related Shows
     *
     * @param $imdb_id
     *
     * @return array
     */
    protected function getRelated($imdb_id)
    {
        $related_imdb_ids = [];

        $url = 'https://imdb.com/title/' . $imdb_id;

        // Create DOM from URL
        $simple_html_dom = new SimpleHtmlDom();
        $html = $simple_html_dom->file_get_html($url);
        // Start Related Shows Loop
        foreach ($html->find('[data-cel-widget="StaticFeature_MoreLikeThis"] .ipc-poster-card > a') as $show) {
            // Get related show imdb id
            $related_imdb_ids[] = $show->attr['data-tconst'];
        }

        return $related_imdb_ids;
    }


    /**
     * Updates Cast Known For
     *
     * @param $cast_id
     * @param $tmdb_id
     * @param $media_type
     *
     * @return bool
     * @throws \Exception
     */
    protected function updateKnownFor($cast_id, $tmdb_id, $media_type){
        // Save new data
        $model = null;
        switch($media_type){
            case 'tv':
                $tv = Tmdb::getTmdbTv($tmdb_id, 'external_ids');
                $imdb_id = ArrayHelper::getValue($tv, 'external_ids.imdb_id');

                if (empty($imdb_id)) {
                    return false;
                }

                $show = Shows::findOne([
                    'imdb_id' => $imdb_id
                ]);

                if(!$show){
                    return false;
                }

                $model = new KnownForShows();
                $model->id_show = $show->id_show;
                break;
            case 'movie':
                $movie = Tmdb::getMovie($tmdb_id);

                $imdb_id = ArrayHelper::getValue($movie, 'imdb_id');

                if (empty($imdb_id)) {
                    return  false;
                }

                $movie = Movies::findOne([
                    'imdb_id' => sanitize_imdb_id($imdb_id)
                ]);

                if(!$movie){
                    return false;
                }

                $model = new KnownForMovies();
                $model->id_movie = $movie->id_movie;
                break;
        }

        if (empty($model)) {
            return false;
        }

        $model->id_cast = $cast_id;
        return $model->save(false);
    }

    /**
     * @param $contents
     *
     * @return string|null
     */
    protected function upload_backdrop($contents)
    {
        if (empty($contents) || !$contents) {
            return null;
        }

        $uploadInfo = $this->ImageStorage->handleBackdropUpload($contents);
        if ($uploadInfo['success'] == true && !empty($uploadInfo['path'])) {
            return '/' . $uploadInfo['path'];
        }

        return null;
    }

    /**
     * @param $contents
     *
     * @return string|null
     */
    protected function upload_poster($contents)
    {
        if (empty($contents) || !$contents) {
            return null;
        }

        $uploadInfo = $this->ImageStorage->handlePosterUpload($contents);
        if ($uploadInfo['success'] == true && !empty($uploadInfo['path'])) {
            return '/' . $uploadInfo['path'];
        }

        return null;
    }

    /**
     * @param $contents
     * @param ImageStorage $ImageStorage
     *
     * @return string|null
     */
    protected function upload_face($contents, ImageStorage $ImageStorage)
    {
        if (empty($contents) || !$contents) {
            return null;
        }

        $uploadInfo = $this->ImageStorage->handleFaceUpload($contents);
        if ($uploadInfo['success'] == true && !empty($uploadInfo['path'])) {
            return '/' . $uploadInfo['path'];
        }

        return null;
    }


    /**
     * Create Instance of Imdb Parser
     *
     * @param $imdbId
     *
     * @return ImdbTitle
     */
    protected function initImdbTitle($imdbId)
    {
        $config = new ImdbConfig();
        $config->language = 'en-US';
        $config->usecache = false;
        $config->debug = true;
        if (!empty($this->injectedProxy)) {
            print('Using Proxy: ' . $this->injectedProxy . PHP_EOL);
            $proxy_parts = explode(':', $this->injectedProxy);
//            $config->use_proxy = true;
            $config->proxy_host = $proxy_parts['0'];
            $config->proxy_port = $proxy_parts['1'];
        }
        $config->cache_expire = 86400;

        return new ImdbTitle($imdbId, $config);
    }
}
