<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "shows_save_queue_external".
 *
 * @property int $id
 * @property int $id_tvshow
 * @property int $id_episode
 * @property int $episode
 * @property int $season
 * @property string $remote_ip
 * @property string $files
 * @property string|null $original_language
 * @property string $rel_title
 * @property string $slug
 * @property int|null $status
 * @property int|null $is_dd
 * @property int|null $flag_quality
 * @property int $id_process
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $subs
 */
class ShowsSaveQueueExternal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_save_queue_external';
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
            [['id_tvshow', 'id_episode', 'episode', 'season', 'remote_ip', 'files', 'rel_title', 'slug', 'id_process'], 'required'],
            [['id_tvshow', 'id_episode', 'episode', 'season', 'status', 'is_dd', 'flag_quality', 'id_process'], 'integer'],
            [['files', 'created_at', 'updated_at', 'subs'], 'safe'],
            [['remote_ip', 'rel_title', 'slug'], 'string', 'max' => 255],
            [['original_language'], 'string', 'max' => 80],
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
            'files' => 'Files',
            'original_language' => 'Original Language',
            'rel_title' => 'Rel Title',
            'slug' => 'Slug',
            'status' => 'Status',
            'is_dd' => 'Is Dd',
            'flag_quality' => 'Flag Quality',
            'id_process' => 'Id Process',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'subs' => 'Subs',
        ];
    }
}
