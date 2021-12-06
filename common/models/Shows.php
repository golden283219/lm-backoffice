<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shows".
 *
 * @property int $id_show
 * @property int $is_active
 * @property string $title
 * @property string $description
 * @property int $year
 * @property string $first_air_date
 * @property string $poster
 * @property string $backdrop
 * @property string $country
 * @property string $imdb_id
 * @property double $imdb_rating
 * @property int $tmdb_id
 * @property int $duration
 * @property int $has_metadata
 * @property string $homepage
 * @property string $youtube
 * @property string $slug
 * @property int $in_production
 * @property int $views
 * @property array $cast
 * @property array $genres
 * @property string $date_added
 */
class Shows extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_show', 'imdb_id'], 'required'],
            [['id_show', 'is_active', 'year', 'tmdb_id', 'duration', 'has_metadata', 'in_production', 'views'], 'integer'],
            [['description'], 'string'],
            [['first_air_date', 'cast', 'genres', 'date_added'], 'safe'],
            [['imdb_rating'], 'number'],
            [['title'], 'string', 'max' => 100],
            [['poster', 'backdrop'], 'string', 'max' => 80],
            [['country'], 'string', 'max' => 80],
            [['imdb_id'], 'string', 'max' => 20],
            [['homepage', 'slug'], 'string', 'max' => 255],
            [['youtube'], 'string', 'max' => 120],
            [['imdb_id'], 'unique'],
            [['id_show'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_show' => 'Id Show',
            'is_active' => 'Is Active',
            'title' => 'Title',
            'description' => 'Description',
            'year' => 'Year',
            'first_air_date' => 'First Air Date',
            'poster' => 'Poster',
            'backdrop' => 'Backdrop',
            'country' => 'Country',
            'imdb_id' => 'Imdb ID',
            'imdb_rating' => 'Imdb Rating',
            'tmdb_id' => 'Tmdb ID',
            'duration' => 'Duration',
            'has_metadata' => 'Has Metadata',
            'homepage' => 'Homepage',
            'youtube' => 'Youtube',
            'slug' => 'Slug',
            'in_production' => 'In Production',
            'views' => 'Views',
            'cast' => 'Cast',
            'genres' => 'Genres',
            'date_added' => 'Date Added',
        ];
    }

    public static function QueryTerm ($term) {

      $where_query = '';

      if ($term !== '') {
        $where_query = ' WHERE title LIKE \'%'.$term.'%\'';
      }

      $SQL = 'SELECT title, id_show, year, backdrop FROM shows' . $where_query . ' LIMIT 20';


      $connection = \Yii::$app->getDb();
      $command = $connection->createCommand($SQL);
      $result = $command->queryAll();
      $connection->close();

      return $result;

    }

}
