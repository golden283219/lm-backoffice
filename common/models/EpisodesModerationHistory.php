<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "episodes_moderation_history".
 *
 * @property int $id
 * @property int $id_meta
 * @property int $id_site_episode
 * @property string $title
 * @property string $imdb_id
 * @property string $tvmaze_id
 * @property string $air_date
 * @property int $episode
 * @property int $season
 * @property int $priority
 * @property string $original_language
 * @property int $id_user
 * @property int $status
 * @property array $data
 * @property string $guid
 * @property int $type
 * @property string $worker_ip
 * @property string $created_at
 * @property string $updated_at
 * @property int $is_deleted
 */
class EpisodesModerationHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'episodes_moderation_history';
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
            [['id_meta', 'id_site_episode', 'episode', 'season', 'priority', 'id_user', 'status', 'type', 'is_deleted'], 'integer'],
            [['air_date', 'data', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['imdb_id', 'tvmaze_id'], 'string', 'max' => 20],
            [['original_language'], 'string', 'max' => 3],
            [['guid'], 'string', 'max' => 100],
            [['worker_ip'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_meta' => 'Id Meta',
            'id_site_episode' => 'Id Site Episode',
            'title' => 'Title',
            'imdb_id' => 'Imdb ID',
            'tvmaze_id' => 'Tvmaze ID',
            'air_date' => 'Air Date',
            'episode' => 'Episode',
            'season' => 'Season',
            'priority' => 'Priority',
            'original_language' => 'Original Language',
            'id_user' => 'Id User',
            'status' => 'Status',
            'data' => 'Data',
            'guid' => 'Guid',
            'type' => 'Type',
            'worker_ip' => 'Worker Ip',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
        ];
    }
}
