<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "moderation_draft_items".
 *
 * @property int $id
 * @property int $id_moderation_draft
 * @property string $data
 * @property string $controller
 * @property string $action
 */
class ModerationDraftItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'moderation_draft_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_moderation_draft', 'controller', 'action'], 'required'],
            [['id_moderation_draft'], 'integer'],
            [['data'], 'string'],
            [['controller', 'action'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_moderation_draft' => 'Id Moderation Draft',
            'data' => 'Data',
            'controller' => 'Controller',
            'action' => 'Action',
        ];
    }
}
