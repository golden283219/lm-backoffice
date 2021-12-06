<?php

namespace api\modules\v1\resources;

use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class Movies extends \api\modules\v1\models\site\Movies implements Linkable
{
    public function fields()
    {
        return [
            'id_movie',
            'slug',
            'storage_slug',
            'shard_url',
            'title',
            'year',
            'date_added',
            'new_converter',
            'is_chromecast_supported',
            'imdb_id' => function ($model) {
                return isset($model->imdb_id) && $model->imdb_id !== '' && $model->imdb_id !== '0' ? 'tt' . $model->imdb_id : null;
            }
        ];
    }

    public function extraFields()
    {
        return [
            'movies_storage' => function ($model) {
                return $model->moviesStorage;
            },
            'movies_subtitles' => function ($model) {
                return $model->moviesSubtitles;
            },
            'movies_audio' => function ($model) {
                return $model->moviesAudio;
            }
        ];
    }

    /**
     * Returns a list of links.
     *
     * @return array the links
     */
    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['movies/view', 'id' => $this->id_movie], true)
        ];
    }
}
