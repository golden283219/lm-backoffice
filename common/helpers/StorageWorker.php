<?php

namespace common\helpers;

use GuzzleHttp\Client;

class StorageWorker
{

    private $client;

    const SUBTITLES_MOVIES_POST_API = '/C0AB7286C792925B1AE40F77E07485C7C3E92BD7/movies-subtitles/index.php';
    const SUBTITLES_EPISODES_POST_API = '/C0AB7286C792925B1AE40F77E07485C7C3E92BD7/shows-subtitles/index.php';
    const SUBTITLES_ACTIONS_POST_API = '/C0AB7286C792925B1AE40F77E07485C7C3E92BD7/subtitles/actions/add.php';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://31.220.26.23'
        ]);
    }

    public function AddAction($action, $data, $subs_path)
    {
        $this->client->POST($this::SUBTITLES_ACTIONS_POST_API, [
            'form_params' => [
                'action' => $action,
                'data' => $data,
                'subs_path' => $subs_path
            ]
        ]);
    }

    public function SendMoviesSubtitles($contents, $language, $storage_slug, $shard_url)
    {
        $response = $this->client->POST($this::SUBTITLES_MOVIES_POST_API, [
            'form_params' => [
                'contents' => $contents,
                'storage' => $shard_url,
                'language' => $language,
                'storage_slug' => $storage_slug,
            ]
        ]);

        return $response->getBody();
    }

    public function SendShowsSubtitles($remote_name, $contents)
    {
        $response = $this->client->POST($this::SUBTITLES_EPISODES_POST_API, [
            'form_params' => [
                'remote_name' => $remote_name,
                'contents' => $contents,
            ]
        ]);
    }

}