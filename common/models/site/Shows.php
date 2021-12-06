<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "shows".
 *
 * @property int $id_show
 * @property int $is_active
 * @property string|null $title
 * @property string|null $description
 * @property int|null $year
 * @property string|null $first_air_date
 * @property string|null $poster
 * @property string|null $backdrop
 * @property string|null $country
 * @property string $imdb_id
 * @property float $imdb_rating
 * @property int|null $tmdb_id
 * @property int|null $duration
 * @property int $has_metadata
 * @property string|null $homepage
 * @property string|null $youtube
 * @property string|null $slug
 * @property int|null $in_production
 * @property int $views
 * @property string|null $cast
 * @property string|null $genres
 * @property string $date_added
 * @property int|null $tvdb_id
 * @property string|null $original_lang
 *
 * @property KnownForShows[] $knownForShows
 * @property ShowsCache[] $showsCaches
 * @property ShowsNotifications[] $showsNotifications
 * @property ShowsRelated[] $showsRelateds
 * @property ShowsRelated[] $showsRelateds0
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
            [['id_show', 'is_active', 'year', 'tmdb_id', 'duration', 'has_metadata', 'in_production', 'views', 'tvdb_id'], 'integer'],
            [['description'], 'string'],
            [['first_air_date', 'cast', 'genres', 'date_added'], 'safe'],
            [['imdb_rating'], 'number'],
            [['title'], 'string', 'max' => 100],
            [['poster', 'backdrop', 'country'], 'string', 'max' => 80],
            [['imdb_id'], 'string', 'max' => 20],
            [['homepage', 'slug'], 'string', 'max' => 255],
            [['youtube'], 'string', 'max' => 120],
            [['original_lang'], 'string', 'max' => 50],
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
            'tvdb_id' => 'Tvdb ID',
            'original_lang' => 'Original Lang',
        ];
    }

    /**
     * Gets query for [[KnownForShows]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKnownForShows()
    {
        return $this->hasMany(KnownForShows::className(), ['id_show' => 'id_show']);
    }

    /**
     * Gets query for [[ShowsCaches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsCaches()
    {
        return $this->hasMany(ShowsCache::className(), ['id_show' => 'id_show']);
    }

    /**
     * Gets query for [[ShowsNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsNotifications()
    {
        return $this->hasMany(ShowsNotifications::className(), ['show_id' => 'id_show']);
    }

    /**
     * Gets query for [[ShowsRelateds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsRelateds()
    {
        return $this->hasMany(ShowsRelated::className(), ['id_show' => 'id_show']);
    }

    /**
     * Gets query for [[ShowsRelateds0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsRelateds0()
    {
        return $this->hasMany(ShowsRelated::className(), ['related_id_show' => 'id_show']);
    }
}
