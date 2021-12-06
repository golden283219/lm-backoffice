<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "shows_episodes_reports_cache".
 *
 * @property int $id
 * @property int|null $count
 * @property int|null $episode_number
 * @property int|null $season_number
 * @property int|null $id_tvshow
 * @property int|null $id_episode
 * @property string|null $last_reported_at
 * @property int|null $assigned_user_id
 * @property int|null $is_closed
 */
class ShowsEpisodesReportsCache extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_episodes_reports_cache';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['count', 'episode_number', 'season_number', 'id_tvshow', 'id_episode', 'assigned_user_id', 'is_closed'], 'integer'],
            [['last_reported_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'count' => 'Count',
            'episode_number' => 'Episode Number',
            'season_number' => 'Season Number',
            'id_tvshow' => 'Id Tvshow',
            'id_episode' => 'Id Episode',
            'last_reported_at' => 'Last Reported At',
            'assigned_user_id' => 'Assigned User ID',
            'is_closed' => 'Is Closed',
        ];
    }

    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id_show' => 'id_tvshow']);
    }
}
