<?php


namespace console\models\site;


use common\models\site\MoviesCast;
use common\models\site\MoviesGenres;
use yii\db\Query;

class Movies extends \common\models\site\Movies
{
    public function getGenres()
    {
        return $this->hasMany(MoviesGenres::className(), ['id_movie' => 'id_movie']);
    }

    /**
     * Get Cast Count
     */
    public function castCount()
    {
        $cast_count = (new Query())
            ->select('id')
            ->from('movies_cast')
            ->where(['id_movie' => $this->id_movie])
            ->count();

        if (!empty($cast_count)) {
            return intval($cast_count, 10);
        }

        return 0;
    }

    /**
     * @param $id_genre
     *
     * @return bool
     */
    public function insertGenre($id_genre)
    {
        $genre = new MoviesGenres();
        $genre->id_movie = $this->id_movie;
        $genre->id_genre = $id_genre;

        return $genre->validate() && $genre->save();
    }

    /**
     * @param $cast_data
     *
     * @return bool
     */
    public function insertCast($cast_data)
    {
        $movies_cast           = new MoviesCast();
        $movies_cast->hero     = $cast_data['hero'];
        $movies_cast->role     = $cast_data['role'];
        $movies_cast->id_cast  = $cast_data['id'];
        $movies_cast->id_movie = $this->id_movie;

        return $movies_cast->validate() &&  $movies_cast->save();
    }
}
