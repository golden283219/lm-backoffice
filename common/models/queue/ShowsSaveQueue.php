<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "shows_save_queue".
 *
 * @property int $id
 * @property int $id_tvshow
 * @property int $id_episode
 * @property int $episode
 * @property int $season
 * @property string $remote_ip
 * @property string $slug
 * @property int $is1080p
 * @property int $is720p
 * @property int $is480p
 * @property int $is360p
 * @property int $status
 * @property string $date_added
 * @property int $flag_quality
 * @property string $rel_title
 * @property string $original_language
 * @property int $is_dd
 * @property int $id_process
 */
class ShowsSaveQueue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_save_queue';
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
            [['id_tvshow', 'id_episode', 'episode', 'season', 'remote_ip', 'slug', 'is1080p', 'is720p', 'is480p', 'is360p'], 'required'],
            [['id_tvshow', 'id_episode', 'episode', 'season', 'is1080p', 'is720p', 'is480p', 'is360p', 'status', 'flag_quality', 'is_dd', 'id_process'], 'integer'],
            [['date_added'], 'safe'],
            [['remote_ip'], 'string', 'max' => 50],
            [['slug'], 'string', 'max' => 255],
            [['rel_title'], 'string', 'max' => 500],
            [['original_language'], 'string', 'max' => 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_tvshow' => 'Id Tvshow',
            'id_episode' => 'Id Episode',
            'episode' => 'Episode',
            'season' => 'Season',
            'remote_ip' => 'Remote Ip',
            'slug' => 'Slug',
            'is1080p' => 'Is1080p',
            'is720p' => 'Is720p',
            'is480p' => 'Is480p',
            'is360p' => 'Is360p',
            'status' => 'Status',
            'date_added' => 'Date Added',
            'flag_quality' => 'Flag Quality',
            'rel_title' => 'Rel Title',
            'original_language' => 'Original Language',
            'is_dd' => 'Is Dd',
            'id_process' => 'Id Process',
        ];
    }
}
