<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "moderation_draft".
 *
 * @property int $id
 * @property int $id_media
 * @property string $title
 * @property int $category
 * @property int $created_by
 * @property int $executed_by
 * @property int $status
 * @property int $created_at
 */
class ModerationDraft extends \yii\db\ActiveRecord
{
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
      [['id_media', 'category', 'created_by', 'executed_by', 'status', 'created_at'], 'integer'],
      [['title'], 'string', 'max' => 255],
    ];
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
      'category' => 'Category',
      'created_by' => 'Created By',
      'executed_by' => 'Executed By',
      'status' => 'Status',
      'created_at' => 'Created At',
    ];
  }

  public function getDraftItems()
  {
    return $this->hasMany(ModerationDraftItems::className(), ['id_moderation_draft' => 'id']);
  }
}
