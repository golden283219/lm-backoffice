<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "shows_episodes_subtitles".
 *
 * @property int $id
 * @property int $id_episode
 * @property string $languageName
 * @property string $shard
 * @property string $isoCode
 * @property string $storagePath
 * @property int $is_approved
 * @property int $is_moderated
 * @property string $hash
 * @property string $release_title
 * @property string|null $source
 * @property int|null $source_id
 * @property float|null $score
 * @property string|null $format
 */
class ShowsEpisodesSubtitles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_episodes_subtitles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_episode', 'languageName', 'shard', 'isoCode', 'storagePath'], 'required'],
            [['id_episode', 'is_approved', 'is_moderated', 'source_id'], 'integer'],
            [['score'], 'number'],
            [['languageName', 'shard', 'release_title'], 'string', 'max' => 100],
            [['isoCode'], 'string', 'max' => 20],
            [['storagePath'], 'string', 'max' => 255],
            [['hash'], 'string', 'max' => 32],
            [['source', 'format'], 'string', 'max' => 50],
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
            'languageName' => 'Language Name',
            'shard' => 'Shard',
            'isoCode' => 'Iso Code',
            'storagePath' => 'Storage Path',
            'is_approved' => 'Is Approved',
            'is_moderated' => 'Is Moderated',
            'hash' => 'Hash',
            'release_title' => 'Release Title',
            'source' => 'Source',
            'source_id' => 'Source ID',
            'score' => 'Score',
            'format' => 'Format',
        ];
    }
}
