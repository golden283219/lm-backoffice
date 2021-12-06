<?php

namespace console\models\queue;

use common\models\MoviesSubtitles;
use common\models\SubtitlesLanguages;
use GuzzleHttp\Psr7\Request;
use Http\Client\Exception;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;
use yii\httpclient\Response;
use yii\queue\Queue;

use console\models\site\Movies;

class MovieQueue extends AbstractQueue
{

    public $imdbId;
    public $movieTitle;
    public $movieYear;

    /**
     * @param Queue $queue which pushed and is handling the job
     */
    public function execute($queue = null) : void
    {
        $requestData = [
            'imdb_id' => $this->imdbId,
            'title' => $this->movieTitle,
            'year' => $this->movieYear,
        ];

        $movieUrl = \Yii::$app->params['titleService']['movies'];
        $response = $this->handlerHttpRequest($movieUrl, $requestData);

        if ($response['status'] == true && !empty($response['data'])) {
            foreach ($response['data'] as $num => $subTitleElement) {
                $fileLocation = $subTitleElement['contents'];

                if (empty($fileLocation)) {
                    \Yii::warning("Failed to get proper content URL: " . print_r($subTitleElement, 1));
                    continue;
                }

                $releaseTitle = $subTitleElement['name'];
                $language = $this->checkSubtitlesLanguage($subTitleElement['language'], $subTitleElement['iso639']);
                $isoCode = $subTitleElement['iso639'];


                // temp fix the URL of the location passed from API
                $fileLocation = str_replace('localhost', '116.202.253.134', $fileLocation);

                $content = $this->gunZipFile($fileLocation);
                if (!$content) {
                    \Yii::warning("Failed to download a gunzip file: $fileLocation");
                    continue;
                }
                $hash = md5($content);

                $movie = Movies::findOne(['imdb_id' => $this->imdbId]);
                if ($movie === null ) {
                    \Yii::warning("Failed to find movie with imdb: $this->imdbId");
                    continue;
                }
                $subtitles = $movie->getSubtitles()->where(['=', 'hash', $hash])->all();

                if (empty($subtitles)) {
                    $path = str_replace('storage', 'stor', $movie->shard_url);
                    $url = $path . implode("/", ['movies', $movie->slug, 'subtitles', $isoCode . "_" . $hash . ".vtt"]);
                    $title = new MoviesSubtitles();
                    $title->id_movie = $movie->id_movie;
                    $title->url = $url;
                    $title->language = $language;
                    $title->shard = str_replace("/", "", $movie->shard_url);
                    $title->hash = $hash;
                    $title->release_title = $releaseTitle;
                    $saved = $title->validate() && $title->save();

                    if (!$saved) {
                        \Yii::warning("Failed to save subtitles: " . print_r($subTitleElement, 1));
                    } else {
                        $uploaded = $this->storeFile($url, $content);
                        if (!$uploaded) {
                            \Yii::warning("Failed to upload file to storage: " . $url);
                        }
                    }
                }
            }
        } else if ($response['status'] == 2) {
            \Yii::warning(print_r($response, 1));
        }
    }
}