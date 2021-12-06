<?php

namespace backend\modules\moderation\models;

use Yii;

/**
 * This is the model class for table "movies".
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string $imdb_id
 * @property string $url
 * @property int $is_downloaded
 * @property string $source
 * @property string $bad_guids
 * @property string $bad_titles
 * @property int $priority
 * @property int $flag_quality
 * @property string $worker_ip
 * @property string $updated_at
 * @property string $original_language
 */
class MoviesDownloadQueue extends \yii\db\ActiveRecord
{

    const STATUS_DOWNLOADED = 1;
    const STATUS_WAITING_TORRENT_DOWNLOADER= 13;
    const STATUS_MISSING_DOWNLOAD_CANDIDATE = 14;
    const STATUS_BEING_CONVERTED = 3;
    const STATUS_WAITING_USENET_DOWNLOADER = 10;    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_queue');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'year', 'url'], 'required'],
            [['year', 'is_downloaded', 'priority', 'flag_quality'], 'integer'],
            [['bad_guids'], 'string'],
            [['updated_at'], 'safe'],
            [['title', 'url'], 'string', 'max' => 255],
            [['imdb_id', 'original_language'], 'string', 'max' => 120],
            [['source'], 'string', 'max' => 1],
            [['bad_titles'], 'string', 'max' => 1200],
            [['worker_ip'], 'string', 'max' => 50],
            [['imdb_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'year' => 'Year',
            'imdb_id' => 'Imdb ID',
            'url' => 'Url',
            'is_downloaded' => 'Download Status',
            'source' => 'Source',
            'bad_guids' => 'Bad Guids',
            'bad_titles' => 'Bad Titles',
            'priority' => 'Priority',
            'flag_quality' => 'Flag Quality',
            'worker_ip' => 'Worker Ip',
            'updated_at' => 'Updated At',
            'original_language' => 'Original Language',
        ];
    }
}
