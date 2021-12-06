<?php

namespace common\models\queue;

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
 * @property string $torrent_blob
 * @property int $type
 * @property string $rel_title
 * @property string $history_guid
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
            [['year', 'is_downloaded', 'priority', 'flag_quality', 'type'], 'integer'],
            [['bad_guids', 'torrent_blob'], 'string'],
            [['updated_at'], 'safe'],
            [['title', 'url', 'rel_title'], 'string', 'max' => 255],
            [['imdb_id', 'original_language'], 'string', 'max' => 120],
            [['source'], 'string', 'max' => 1],
            [['bad_titles'], 'string', 'max' => 1200],
            [['worker_ip'], 'string', 'max' => 50],
            [['history_guid'], 'string', 'max' => 100],
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
            'is_downloaded' => 'Is Downloaded',
            'source' => 'Source',
            'bad_guids' => 'Bad Guids',
            'bad_titles' => 'Bad Titles',
            'priority' => 'Priority',
            'flag_quality' => 'Flag Quality',
            'worker_ip' => 'Worker Ip',
            'updated_at' => 'Updated At',
            'original_language' => 'Original Language',
            'torrent_blob' => 'Torrent Blob',
            'type' => 'Type',
            'rel_title' => 'Rel Title',
            'history_guid' => 'History Guid',
        ];
    }
}
