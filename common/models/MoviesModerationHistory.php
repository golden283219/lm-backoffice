<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "movies_moderation_history".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $title
 * @property string $imdb_id
 * @property int $year
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
class MoviesModerationHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_moderation_history';
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
            [['id_movie', 'year', 'priority', 'id_user', 'status', 'type', 'is_deleted'], 'integer'],
            [['data', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['imdb_id'], 'string', 'max' => 20],
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
            'id_movie' => 'Id Movie',
            'title' => 'Title',
            'imdb_id' => 'Imdb ID',
            'year' => 'Year',
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
