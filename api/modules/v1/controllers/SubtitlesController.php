<?php

namespace api\modules\v1\controllers;

use Yii;
use GuzzleHttp\Client;
use yii\web\Controller;
use yii\web\UploadedFile;
use common\models\site\Movies;
use common\models\site\MoviesSubtitles;
use common\models\site\ShowsEpisodes;
use common\models\site\ShowsEpisodesSubtitles;

class SubtitlesController extends Controller
{
    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    // restrict access to
                    'Origin' => [
                        'http://v1.lookmovie.ag', 'https://v1.lookmovie.ag',
                        'http://lookmovie.io', 'https://lookmovie.io',
                        'http://lookmovie.ag', 'https://lookmovie.ag',
                        'http://lookmovie.art', 'https://lookmovie.art',
                        'http://lookmovie.best', 'https://lookmovie.best',
                        'http://lookmovie.click', 'https://lookmovie.click',
                        'http://lookmovie.clinic', 'https://lookmovie.clinic',
                        'http://lookmovie.digital', 'https://lookmovie.digital',
                        'http://lookmovie.download', 'https://lookmovie.download',
                        'http://lookmovie.foundation', 'https://lookmovie.foundation',
                        'http://lookmovie.fun', 'https://lookmovie.fun',
                        'http://lookmovie.fyi', 'https://lookmovie.fyi',
                        'http://lookmovie.guru', 'https://lookmovie.guru',
                        'http://lookmovie.mobi', 'https://lookmovie.mobi',
                    ],
                ],
            ],
        ];
    }

    /**
     * Converts SRT to VTT
     */
    public function actionSrtToVtt()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->set('Access-Control-Allow-Origin', '*');

        $response = '';

        if (isset($_FILES['srt']) && file_exists($_FILES['srt']['tmp_name'])) {
            $response = $this->srt_to_vtt($_FILES['srt']['tmp_name']);
        }

        return $response;
    }

    /**
     * Endpoint to upload subtitle for tv episode
     *
     * @param $id_episode
     *
     * @return array
     */
    public function actionUploadEpisodeOpensub($id_episode)
    {
        $post = Yii::$app->request->post();

        $uploaded_file = UploadedFile::getInstanceByName('file');

        if (empty($uploaded_file) || empty($post['subtitle'])) {
            return [
                'success' => false,
                'message' => 'missing required fields'
            ];
        }

        $openSubtitle = json_decode($post['subtitle']);

        $file_name = $this->ungzip($uploaded_file->tempName, $uploaded_file->tempName . '.vtt');
        $content = $this->srt_to_vtt($file_name);

        $episode = ShowsEpisodes::find()
            ->select('shows_episodes.*, shows.imdb_id')
            ->where(['id' => $id_episode])
            ->leftJoin('shows', 'shows.id_show=shows_episodes.id_shows')
            ->one();

        foreach ($episode->storage as $storage_item) {
            $parts = explode('/', $storage_item);
            $episode_storage_path = "{$parts[0]}/{$parts[1]}/{$parts[2]}";
            break;
        }

        $hash = md5($content);

        $fixed_url = "$episode_storage_path/subtitles/";
        $path = $fixed_url . $openSubtitle->language_id . "_" . $hash . '.vtt';

        // Check subtitle
        $title = ShowsEpisodesSubtitles::find()->where([
            'id_episode' => $episode->id,
            'source' => 'opensubtitle',
            'source_id' => $openSubtitle->opensubtitle_id,
        ])->asArray()->one();
        if ($title) {
            return [
                'success' => true,
                'message' => 'Subtitle Already Exists',
                'url'     => '/'.$title['shard'].'/'.$title['storagepath']
            ];
        }

        // Insert subtitle
        $title = new ShowsEpisodesSubtitles();
        $title->id_episode = $episode->id;
        $title->languageName = ucfirst($openSubtitle->language);
        $title->shard = $episode->shard;
        $title->isoCode = $openSubtitle->language_id;
        $title->storagePath = $path;
        $title->hash = $hash;
        $title->is_approved = 1;
        $title->release_title = $openSubtitle->release_title;

        // Set source data
        $title->source = 'opensubtitle';
        $title->source_id = $openSubtitle->opensubtitle_id;
        $title->score = $openSubtitle->score;
        $title->format = $openSubtitle->format;

        // Upload file content to storage
        $url = "/" . str_replace('storage', 'stor', $episode->shard) . "/" . $path;
        $uploaded = $this->storeFile($url, $content);

        if ($uploaded && $title->validate() && $title->save()) {
            return [
                'success' => true,
                'message' => 'Successfully uploaded',
                'url' => "/{$title['shard']}/{$title['storagePath']}"
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to upload file to storage: ' . $url
        ];
    }

    /**
     * Endpoint to upload subtitle to Movie
     * @param $id_movie
     * @return array
     */
    public function actionUploadMovieOpensub($id_movie)
    {
        $post = Yii::$app->request->post();

        $uploaded_file = UploadedFile::getInstanceByName('file');

        if (empty($uploaded_file) || empty($post['subtitle'])) {
            return [
                'success' => false,
                'message' => 'missing required fields'
            ];
        }

        $openSubtitle = json_decode($post['subtitle']);

        $file_name = $this->ungzip($uploaded_file->tempName, $uploaded_file->tempName . '.vtt');
        $content = $this->srt_to_vtt($file_name);

        // Find Movie Subtitle Belongs To
        $movie = Movies::find()->where(['id_movie' => $id_movie])->one();

        // Check subtitle
        $title = MoviesSubtitles::find()->where([
            'id_movie' => $id_movie,
            'source' => 'opensubtitle',
            'source_id' => $openSubtitle->opensubtitle_id,
        ])->one();

        if ($title) {
            if (isset($title->shard)) {
                $url = "/{$title->shard}/{$title->url}";
            } else {
                $url_parts = explode('/', $title->url);
                $url = $movie->shard_url . "movies/{$url_parts['1']}/subtitles/{$url_parts['2']}.vtt";
            }
            // subtitle real path
            return [
                'success' => true,
                'message' => 'File already exists.',
                'url'     => $url
            ];
        }

        $hash = md5($content);
        $url = implode("/", ['movies', $movie->storage_slug, 'subtitles', $openSubtitle->language_id . "_" . $hash . ".vtt"]);

        $title = new MoviesSubtitles();
        $title->id_movie = $movie->id_movie;
        $title->url = $url;
        $title->language = ucfirst($openSubtitle->language);
        $title->shard = str_replace("/", "", $movie->shard_url);
        $title->hash = $hash;
        $title->is_approved = 1;
        $title->release_title = $openSubtitle->release_title;

        // Set source data
        $title->source = 'opensubtitle';
        $title->source_id = $openSubtitle->opensubtitle_id;
        $title->score = $openSubtitle->score;
        $title->format = $openSubtitle->format;

        // Upload file content to storage
        $path = str_replace('storage', 'stor', $movie->shard_url);
        $storage_url = $path . implode("/", ['movies', $movie->storage_slug, 'subtitles', $openSubtitle->language_id . "_" . $hash . ".vtt"]);

        $uploaded = $this->storeFile($storage_url, $content);

        if ($file_name && file_exists($file_name)) {
            unlink($file_name);
        }

        if ($uploaded && $title->validate() && $title->save()) {
            if (isset($title->shard)) {
                $url = "/{$title->shard}/{$title->url}";
            } else {
                $url_parts = explode('/', $title->url);
                $url = $this->shard_url . "movies/{$url_parts['1']}/subtitles/{$url_parts['2']}.vtt";
            }

            return [
                'success' => true,
                'language' => $title['language'],
                'url' => $url
            ];
        }

        return [
            "success" => false,
            "message" => "Failed to upload file to storage: " . $url
        ];
    }

    /**
     * Store file into storage by filePath
     * sends a curl request to stand alone server
     *
     * @param string $filePath
     * @param string $content
     *
     * @return bool
     */
    protected function storeFile(string $filePath, string $content): bool
    {
        $name = explode('/', $filePath);
        $name = end($name);

        // store file into some location
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            env('SUBTITLES_MANAGEMENT_API_ENTRY') . '/upload',
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $content,
                        'filename' => $name,
                    ],
                    [
                        'name' => 'filePath',
                        'contents' => $filePath
                    ],
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        $answer = json_decode($contents, true);

        $uploaded = true;
        if (empty($answer) || $answer['success'] != 1) {
            print("Failed to load title file: " . $answer['message']);
            $uploaded = false;
        }

        return $statusCode === 200 && $uploaded;
    }

    /**
     * @param $source
     * @param null $out_file_name
     *
     * @return string|null
     */
    private function ungzip($source, $out_file_name = null)
    {
        if (empty($out_file_name)) {
            $out_file_name = sys_get_temp_dir() . '/' . GUIDv4();
        }
        // Raising this value may increase performance
        $buffer_size = 12000;

        // Open our files (in binary mode)
        $file = gzopen($source, 'rb');
        $out_file = fopen($out_file_name, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
            // Read buffer-size bytes
            // Both fwrite and gzread and binary-safe
            fwrite($out_file, gzread($file, $buffer_size));
        }

        // Files are done, close files
        fclose($out_file);
        gzclose($file);

        return $out_file_name;
    }

    /**
     * Read *.srt content and sends converts to *.vtt
     *
     * @param $srt_path
     *
     * @return string
     */
    private function srt_to_vtt($srt_path)
    {
        $info = pathinfo($srt_path);
        try {
            $contents = get_bin_file_contents($srt_path);
        } catch (Exception $e) {}

        if (!empty($contents)) {
            $client = new Client();
            $body = $client->post(env('SUBTITLES_SRT_TO_VTT_ENDPOINT'), [
                'multipart' => [
                    [
                        'name' => 'subtitle',
                        'contents' => $contents,
                        'filename' => $info['basename'],
                        'headers'  => [
                            'Content-Type' => 'application/x-subrip'
                        ]
                    ],
                ]
            ]);

            return $body->getBody();
        }

        return '';
    }
}