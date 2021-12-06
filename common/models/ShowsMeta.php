<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shows_meta".
 *
 * @property int $id_meta
 * @property int $id_tvshow
 * @property int $season
 * @property int $episode
 * @property string $title
 * @property string $air_date
 * @property string $worker_ip
 * @property string $bad_guids
 * @property string $bad_titles
 * @property int $state
 * @property int $priority
 * @property string $torrent_blob
 * @property int $type
 * @property string $rel_title
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
            [['id_tvshow', 'season', 'episode', 'state', 'priority', 'type', 'flag_quality'], 'integer'],
            [['air_date'], 'safe'],
            [['torrent_blob'], 'string'],
            [['title'], 'string', 'max' => 120],
            [['worker_ip'], 'string', 'max' => 50],
            [['bad_guids', 'bad_titles'], 'string', 'max' => 7000],
            [['rel_title'], 'string', 'max' => 255],
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
            'flag_quality' => 'Flag Quality',
            'bad_guids' => 'Bad Guids',
            'bad_titles' => 'Bad Titles',
            'state' => 'State',
            'priority' => 'Priority',
            'torrent_blob' => 'Torrent Blob',
            'type' => 'Type',
            'rel_title' => 'Rel Title',
        ];
    }
}
