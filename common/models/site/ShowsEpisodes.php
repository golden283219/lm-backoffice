<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "shows_episodes".
 *
 * @property int $id
 * @property int $id_shows
 * @property int $is_active
 * @property int $episode
 * @property int $season
 * @property string|null $title
 * @property string|null $description
 * @property string|null $still_path
 * @property string|null $shard
 * @property string|null $storage
 * @property int $subtitles_state
 * @property string|null $air_date
 * @property int|null $has_metadata
 * @property int $flag_quality
 * @property string|null $rel_title
 * @property int $is_locked
 * @property int $quality_approved
 * @property int $finalized_subs
 * @property int $have_all_subs
 * @property int $missing_languages
 * @property int $subs_count
 * @property int|null $locked_by
 * @property int|null $locked_at
 * @property int|null $is_chromecast_supported
 * @property int|null $chunks_5s_fixed
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class ShowsEpisodes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_episodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_shows', 'episode', 'season'], 'required'],
            [['id_shows', 'is_active', 'episode', 'season', 'subtitles_state', 'has_metadata', 'flag_quality', 'is_locked', 'quality_approved', 'finalized_subs', 'have_all_subs', 'missing_languages', 'subs_count', 'locked_by', 'locked_at', 'is_chromecast_supported', 'chunks_5s_fixed'], 'integer'],
            [['description'], 'string'],
            [['storage', 'air_date', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 500],
            [['still_path'], 'string', 'max' => 120],
            [['shard'], 'string', 'max' => 80],
            [['rel_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_shows' => 'Id Shows',
            'is_active' => 'Is Active',
            'episode' => 'Episode',
            'season' => 'Season',
            'title' => 'Title',
            'description' => 'Description',
            'still_path' => 'Still Path',
            'shard' => 'Shard',
            'storage' => 'Storage',
            'subtitles_state' => 'Subtitles State',
            'air_date' => 'Air Date',
            'has_metadata' => 'Has Metadata',
            'flag_quality' => 'Flag Quality',
            'rel_title' => 'Rel Title',
            'is_locked' => 'Is Locked',
            'quality_approved' => 'Quality Approved',
            'finalized_subs' => 'Finalized Subs',
            'have_all_subs' => 'Have All Subs',
            'missing_languages' => 'Missing Languages',
            'subs_count' => 'Subs Count',
            'locked_by' => 'Locked By',
            'locked_at' => 'Locked At',
            'is_chromecast_supported' => 'Is Chromecast Supported',
            'chunks_5s_fixed' => 'Chunks 5s Fixed',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
