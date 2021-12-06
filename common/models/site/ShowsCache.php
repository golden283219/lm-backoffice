<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "shows_cache".
 *
 * @property int $id
 * @property int $id_show
 * @property int $latest_season
 * @property int $latest_season_episodes_qty
 * @property string|null $latest_episode_air_date
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property Shows $show
 */
class ShowsCache extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_cache';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_show'], 'required'],
            [['id_show', 'latest_season', 'latest_season_episodes_qty'], 'integer'],
            [['latest_episode_air_date', 'created_at', 'updated_at'], 'safe'],
            [['id_show'], 'exist', 'skipOnError' => true, 'targetClass' => Shows::className(), 'targetAttribute' => ['id_show' => 'id_show']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_show' => 'Id Show',
            'latest_season' => 'Latest Season',
            'latest_season_episodes_qty' => 'Latest Season Episodes Qty',
            'latest_episode_air_date' => 'Latest Episode Air Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Show]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShow()
    {
        return $this->hasOne(Shows::className(), ['id_show' => 'id_show']);
    }
}
