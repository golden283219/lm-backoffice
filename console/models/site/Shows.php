<?php

namespace console\models\site;

use common\models\site\ShowsEpisodes;
use common\models\site\ShowsGenres;
use common\models\site\ShowsCast;

class Shows extends \common\models\site\Shows
{
    public function insertGenre($genre_id)
    {
        $show_genres = new ShowsGenres();
        $show_genres->id_genre = $genre_id;
        $show_genres->id_show = $this->id_show;

        return $this->validate() && $this->save();
    }

    public static function getShowsWithoutMeta()
    {
        $query = (new \yii\db\Query())
            ->select([
                'shows.id_show',
                'shows.imdb_id',
                'shows.tmdb_id',
            ])
            ->from('shows')
            ->where(['shows.has_metadata' => 0])
            ->groupBy('shows.id_show');

        return $query->all();
    }

    public static function getShowsEpisodesWithoutAirDate()
    {
        $query = (new \yii\db\Query())
            ->select([
                'shows_episodes.id_shows',
                'shows.id_show',
                'shows.imdb_id',
                'shows.tmdb_id',
            ])
            ->from('shows_episodes')
            ->where(['shows_episodes.air_date' => null])
            ->groupBy('shows_episodes.id_shows');

        $query->leftJoin('shows', 'shows.id_show = shows_episodes.id_shows');

        return $query->all();
    }

    /**
     * Get Cast Count
     */
    public function castCount()
    {
        $cast_count = (new \yii\db\Query())
            ->select('id')
            ->from('shows_cast')
            ->where(['id_show' => $this->id_show])
            ->count();

        if (!empty($cast_count)) {
            return intval($cast_count, 10);
        }

        return 0;
    }

    public static function getShowsEpisodesWithoutMeta()
    {
        $query = (new \yii\db\Query())
            ->select([
                'shows_episodes.id_shows',
                'shows.id_show',
                'shows.imdb_id',
                'shows.tmdb_id',
            ])
            ->from('shows_episodes')
            ->where(['shows_episodes.has_metadata' => 0])
            ->groupBy('shows_episodes.id_shows');

        $query->leftJoin('shows', 'shows.id_show = shows_episodes.id_shows');

        return $query->all();
    }

    /**
     * @param $cast_data
     *
     * @return bool
     */
    public function insertCast($cast_data)
    {
        $shows_cast           = new ShowsCast();
        $shows_cast->hero     = $cast_data['hero'];
        $shows_cast->role     = $cast_data['role'];
        $shows_cast->id_cast  = $cast_data['id'];
        $shows_cast->id_show = $this->id_show;

        return $shows_cast->validate() &&  $shows_cast->save();
    }
}
