<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "shows_meta".
 *
 * @property int $id_meta
 * @property int $id_tvshow
 * @property int $season
 * @property int $episode
 * @property string|null $title
 * @property string|null $air_date
 * @property string|null $worker_ip
 * @property string|null $bad_guids
 * @property string|null $bad_titles
 * @property int|null $state
 * @property int|null $priority
 * @property string|null $torrent_blob
 * @property int $type
 * @property string|null $rel_title
 * @property string|null $history_guid
 * @property int|null $flag_quality
 * @property string|null $link
 * @property int|null $size
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $map
 */
class ShowsMeta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_meta';
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
            [['id_tvshow', 'season', 'episode'], 'required'],
            [['id_tvshow', 'season', 'episode', 'state', 'priority', 'type', 'flag_quality', 'size'], 'integer'],
            [['air_date', 'created_at', 'updated_at'], 'safe'],
            [['torrent_blob', 'link'], 'string'],
            [['title'], 'string', 'max' => 120],
            [['worker_ip'], 'string', 'max' => 50],
            [['bad_guids', 'bad_titles'], 'string', 'max' => 7000],
            [['rel_title'], 'string', 'max' => 255],
            [['history_guid'], 'string', 'max' => 100],
            [['map'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_meta' => 'Id Meta',
            'id_tvshow' => 'Id Tvshow',
            'season' => 'Season',
            'episode' => 'Episode',
            'title' => 'Title',
            'air_date' => 'Air Date',
            'worker_ip' => 'Worker Ip',
            'bad_guids' => 'Bad Guids',
            'bad_titles' => 'Bad Titles',
            'state' => 'State',
            'priority' => 'Priority',
            'torrent_blob' => 'Torrent Blob',
            'type' => 'Type',
            'rel_title' => 'Rel Title',
            'history_guid' => 'History Guid',
            'flag_quality' => 'Flag Quality',
            'link' => 'Link',
            'size' => 'Size',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'map' => 'Map',
        ];
    }
}
