<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shows_episodes".
 *
 * @property int $id
 * @property int $id_shows
 * @property int $is_active
 * @property int $episode
 * @property int $season
 * @property string $title
 * @property string $description
 * @property string $still_path
 * @property string $shard
 * @property array $storage
 * @property int $subtitles_state
 * @property string $air_date
 * @property int $has_metadata
 * @property int $flag_quality
 * @property string $rel_title
 * @property int $is_locked
 * @property int $quality_approved
 * @property int $finalized_subs
 * @property int $have_all_subs
 * @property int $missing_languages
 * @property int $subs_count
 * @property int $locked_by
 * @property int $locked_at
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
        [['id_shows', 'is_active', 'episode', 'season', 'subtitles_state', 'has_metadata', 'flag_quality', 'is_locked', 'quality_approved', 'finalized_subs', 'have_all_subs', 'missing_languages', 'subs_count', 'locked_by', 'locked_at'], 'integer'],
        [['description'], 'string'],
        [['storage', 'air_date'], 'safe'],
        [['title', 'still_path'], 'string', 'max' => 120],
        [['shard'], 'string', 'max' => 80],
        [['rel_title'], 'string', 'max' => 255],
      ];
    }

    public function getShow()
    {
      return $this->hasOne(Shows::className(), ['id_show' => 'id_shows']);
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
      ];
    }
  }
