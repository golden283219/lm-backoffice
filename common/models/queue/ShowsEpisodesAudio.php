<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "shows_episodes_audio".
 *
 * @property int $id
 * @property int $id_episode
 * @property string $shard
 * @property string $storage_path
 * @property int $type
 */
class ShowsEpisodesAudio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_episodes_audio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_episode', 'shard', 'storage_path', 'type'], 'required'],
            [['id_episode', 'type'], 'integer'],
            [['shard'], 'string', 'max' => 50],
            [['storage_path'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_episode' => 'Id Episode',
            'shard' => 'Shard',
            'storage_path' => 'Storage Path',
            'type' => 'Type',
        ];
    }
}
