<?php

namespace common\models;

use app\models\MoviesFavourites;
use app\models\UserWatching;

use Yii;

/**
 * This is the model class for table "movies".
 *
 * @property int $id_movie
 * @property string $slug
 * @property int $is_active
 * @property string $shard_url
 * @property int $year
 * @property string $title
 * @property string $description
 * @property int $duration
 * @property double $imdb_rating
 * @property string $country
 * @property string $backdrop
 * @property string $homepage
 * @property int $budget
 * @property string $tagline
 * @property string $poster
 * @property int $views
 * @property string $date_added
 * @property int $tmdb_prefix
 * @property int $has_metadata
 * @property int $has_subtitles
 * @property string $rel_title
 * @property string $rel_os_hash
 * @property int $rel_size_bytes
 * @property string $youtube
 * @property int $has_hash
 * @property int $priority
 * @property string $storage_slug
 * @property array $cast
 * @property array $genres
 * @property int $flag_quality
 * @property string $release_date
 * @property string $imdb_id
 * @property int $transfer_status
 * @property int $new_converter
 * @property int $is_chromecast_supported
 */
class Movies extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'movies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_movie', 'slug'], 'required'],
            [['id_movie', 'is_active', 'year', 'is_chromecast_supported', 'duration', 'budget', 'views', 'tmdb_prefix', 'has_metadata', 'has_subtitles', 'rel_size_bytes', 'has_hash', 'priority', 'flag_quality'], 'integer'],
            [['description', 'cast', 'genres'], 'string'],
            [['imdb_rating'], 'number'],
            [['date_added', 'release_date'], 'safe'],
            [['slug', 'shard_url', 'title', 'tagline', 'rel_os_hash', 'storage_slug'], 'string', 'max' => 255],
            [['country', 'youtube'], 'string', 'max' => 80],
            [['backdrop', 'poster'], 'string', 'max' => 120],
            [['homepage'], 'string', 'max' => 240],
            [['rel_title'], 'string', 'max' => 400],
            [['imdb_id'], 'string', 'max' => 40],
            [['transfer_status', 'new_converter'], 'string', 'max' => 4],
            [['id_movie'], 'unique'],
            [['slug'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
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
            'is_chromecast_supported' => 'Is Chromecast Supported',
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
        ];
    }
}
