<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "shows".
 *
 * @property int $id_tvshow
 * @property string $title
 * @property string $first_air_date
 * @property string $imdb_id
 * @property string $tmdb_id
 * @property string $tvmaze_id
 * @property string $tvdb_id
 * @property int $total_episodes
 * @property int $total_seasons
 * @property int $episode_duration
 * @property int $in_production
 * @property int $status
 * @property int $tvmaze_updated_timestamp
 * @property string $date_added
 * @property string $data
 * @property string $original_language
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
     * @return \yii\db\Connection the database connection used by this AR class.
     *
     * @throws \yii\base\InvalidConfigException
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
            [['first_air_date', 'date_added'], 'safe'],
            [['total_episodes', 'total_seasons', 'episode_duration', 'in_production', 'status', 'tvmaze_updated_timestamp', 'tmdb_id', 'tvdb_id', 'tvmaze_id'], 'integer'],
            [['data'], 'string'],
            [['title'], 'string', 'max' => 120],
            [['imdb_id'], 'string', 'max' => 50],
            [['original_language'], 'string', 'max' => 255],
            [['imdb_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_tvshow' => 'Id Tvshow',
            'title' => 'Title',
            'first_air_date' => 'First Air Date',
            'imdb_id' => 'Imdb ID',
            'tmdb_id' => 'Tmdb ID',
            'tvmaze_id' => 'Tvmaze ID',
            'tvmaze_updated_timestamp' => 'Tvmaze Updated Timestamp',
            'total_episodes' => 'Total Episodes',
            'total_seasons' => 'Total Seasons',
            'episode_duration' => 'Episode Duration',
            'in_production' => 'In Production',
            'status' => 'Status',
            'date_added' => 'Date Added',
            'data' => 'Data',
            'original_language' => 'Original Language',
            'tvdb_id' => 'Tvdb Id',
        ];
    }
}
