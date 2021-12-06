<?php

namespace backend\modules\moderation\models;

use Yii;

/**
 * This is the model class for table "moderation_draft".
 *
 * @property int $id
 * @property int $id_media
 * @property string $title
 * @property string $category
 * @property int $created_by
 * @property int $executed_by
 * @property int $is_active
 * @property int $status
 * @property int $created_at
 */
class ModerationDraft extends \yii\db\ActiveRecord
{

    const CATEGORY_MOVIES = 0;
    const CATEGORY_TVSHOWS = 1;
    const CATEGORY_TVEPISODES = 2;

    const STATUS_EXECUTED = 1;
    const STATUS_CANCELED = 2;
    const STATUS_WAITING = 0;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'moderation_draft';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_media', 'created_by', 'created_at'], 'required'],
            [['id_media', 'created_by', 'executed_by', 'status', 'created_at', 'category', 'is_active'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function getDraftItems()
    {
        return $this->hasMany(DraftItems::className(), ['id_moderation_draft' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_media' => 'Id Media',
            'title' => 'Title',
            'is_active' => 'Is Active',
            'category' => 'Category',
            'created_by' => 'Created By',
            'executed_by' => 'Executed By',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
