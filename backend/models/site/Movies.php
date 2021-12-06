<?php

namespace backend\models\site;

class Movies extends \common\models\site\Movies
{

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    public $category = 0;

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_AFTER_INSERT, 'handle_update_site_movie');
        $this->on(self::EVENT_BEFORE_UPDATE, 'handle_update_site_movie');
    }

    public function detachEvents()
    {
        $this->off(self::EVENT_AFTER_INSERT, 'handle_update_site_movie');
        $this->off(self::EVENT_BEFORE_UPDATE, 'handle_update_site_movie');
    }

    public function getMoviesModeration()
    {
        return $this->hasOne(MoviesModeration::className(), ['id_movie' => 'id_movie']);
    }

    public function getSetsData()
    {
        return $this->hasMany(SetsData::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesSubtitles()
    {
        return $this->hasMany(MoviesSubtitles::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesStorage()
    {
        return $this->hasMany(MoviesStorage::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesReports()
    {
        return $this->hasMany(MoviesReports::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesRelated()
    {
        return $this->hasMany(MoviesRelated::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesRelatedReverse()
    {
        return $this->hasMany(MoviesRelated::className(), ['related_id_movie' => 'id_movie']);
    }

    public function getMoviesGenres()
    {
        return $this->hasMany(MoviesGenres::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesFeatured()
    {
        return $this->hasOne(MoviesFeatured::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesDirectors()
    {
        return $this->hasMany(MoviesDirectors::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesAudio()
    {
        return $this->hasMany(MoviesAudio::className(), ['id_movie' => 'id_movie']);
    }

    public function getMoviesActors()
    {
        return $this->hasMany(MoviesActors::className(), ['id_movie' => 'id_movie']);
    }

    public function getModerationDraft()
    {
        return $this->hasMany(ModerationDraft::className(), ['id_media' => 'id_movie', 'category' => 'category']);
    }

}
