<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_moderation".
 *
 * @property int $id
 * @property int $id_movie
 * @property int $quality_approved
 * @property int $finalized_subs
 * @property int $have_all_subs
 * @property array $missing_languages
 * @property int $is_locked
 * @property int $locked_by
 * @property int $locked_at
 * @property int $active_reports_count
 * @property int $latest_reports_timestamp
 */
class MoviesModeration extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_moderation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie'], 'required'],
            [['id_movie', 'quality_approved', 'finalized_subs', 'have_all_subs', 'is_locked', 'locked_by', 'locked_at', 'active_reports_count', 'latest_reports_timestamp'], 'integer'],
            [['missing_languages'], 'safe'],
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
            'quality_approved' => 'Quality Approved',
            'finalized_subs' => 'Finalized Subs',
            'have_all_subs' => 'Have All Subs',
            'missing_languages' => 'Missing Languages',
            'is_locked' => 'Is Locked',
            'locked_by' => 'Locked By',
            'locked_at' => 'Locked At',
            'active_reports_count' => 'Active Reports Count',
            'latest_reports_timestamp' => 'Latest Reports Timestamp',
        ];
    }
    
}
