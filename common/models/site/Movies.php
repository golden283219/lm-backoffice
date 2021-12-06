<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies".
 *
 * @property int $id_movie
 * @property string $slug
 * @property int $is_active
 * @property string $shard_url
 * @property int|null $year
 * @property string|null $title
 * @property string|null $description
 * @property int|null $duration
 * @property float $imdb_rating
 * @property string|null $country
 * @property string|null $backdrop
 * @property string|null $homepage
 * @property int|null $budget
 * @property string|null $tagline
 * @property string|null $poster
 * @property int $views
 * @property string $date_added
 * @property int|null $tmdb_prefix
 * @property int $has_metadata
 * @property int|null $has_subtitles
 * @property string|null $rel_title
 * @property string|null $rel_os_hash
 * @property int|null $rel_size_bytes
 * @property string|null $youtube
 * @property int|null $has_hash
 * @property int|null $priority
 * @property string|null $storage_slug
 * @property string|null $cast
 * @property string|null $genres
 * @property int|null $flag_quality
 * @property string|null $release_date
 * @property string|null $imdb_id
 * @property int|null $transfer_status
 * @property int|null $new_converter
 * @property int|null $is_chromecast_supported
 * @property int|null $temp_moved_images
 * @property int|null $chunks_5s_fixed
 * @property string|null $original_lang
 *
 * @property KnownForMovies[] $knownForMovies
 */
class Movies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie', 'slug'], 'required'],
            [['id_movie', 'is_active', 'year', 'duration', 'budget', 'views', 'tmdb_prefix', 'has_metadata', 'has_subtitles', 'rel_size_bytes', 'has_hash', 'priority', 'flag_quality', 'transfer_status', 'new_converter', 'is_chromecast_supported', 'temp_moved_images', 'chunks_5s_fixed'], 'integer'],
            [['description', 'tagline'], 'string'],
            [['imdb_rating'], 'number'],
            [['date_added', 'cast', 'genres', 'release_date'], 'safe'],
            [['slug', 'shard_url', 'title', 'rel_os_hash', 'storage_slug'], 'string', 'max' => 255],
            [['country', 'youtube'], 'string', 'max' => 80],
            [['backdrop', 'poster'], 'string', 'max' => 120],
            [['homepage'], 'string', 'max' => 240],
            [['rel_title'], 'string', 'max' => 400],
            [['imdb_id'], 'string', 'max' => 40],
            [['original_lang'], 'string', 'max' => 50],
            [['id_movie'], 'unique'],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_movie' => 'Id Movie',
            'slug' => 'Slug',
            'is_active' => 'Is Active',
            'shard_url' => 'Shard Url',
            'year' => 'Year',
            'title' => 'Title',
            'description' => 'Description',
            'duration' => 'Duration',
            'imdb_rating' => 'Imdb Rating',
            'country' => 'Country',
            'backdrop' => 'Backdrop',
            'homepage' => 'Homepage',
            'budget' => 'Budget',
            'tagline' => 'Tagline',
            'poster' => 'Poster',
            'views' => 'Views',
            'date_added' => 'Date Added',
            'tmdb_prefix' => 'Tmdb Prefix',
            'has_metadata' => 'Has Metadata',
            'has_subtitles' => 'Has Subtitles',
            'rel_title' => 'Rel Title',
            'rel_os_hash' => 'Rel Os Hash',
            'rel_size_bytes' => 'Rel Size Bytes',
            'youtube' => 'Youtube',
            'has_hash' => 'Has Hash',
            'priority' => 'Priority',
            'storage_slug' => 'Storage Slug',
            'cast' => 'Cast',
            'genres' => 'Genres',
            'flag_quality' => 'Flag Quality',
            'release_date' => 'Release Date',
            'imdb_id' => 'Imdb ID',
            'transfer_status' => 'Transfer Status',
            'new_converter' => 'New Converter',
            'is_chromecast_supported' => 'Is Chromecast Supported',
            'temp_moved_images' => 'Temp Moved Images',
            'chunks_5s_fixed' => 'Chunks 5s Fixed',
            'original_lang' => 'Original Lang',
        ];
    }

    /**
     * Gets query for [[KnownForMovies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKnownForMovies()
    {
        return $this->hasMany(KnownForMovies::className(), ['id_movie' => 'id_movie']);
    }

    public function getSubtitles()
    {
        return $this->hasMany(MoviesSubtitles::className(), ['id_movie' => 'id_movie']);
    }

    public function getStorage()
    {
        return $this->hasMany(MoviesStorage::className(), ['id_movie' => 'id_movie']);
    }
}
