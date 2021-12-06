<?php

namespace backend\modules\system\models;

use Yii;

/**
 * This is the model class for table "global_messages".
 *
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $type
 * @property int $priority
 * @property int $is_active
 */
class GlobalMessages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'global_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'content'], 'required'],
            [['content', 'date_start', 'date_end'], 'string'],
            [['type', 'priority', 'is_active'], 'integer'],
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
            'title' => 'Title',
            'content' => 'Content',
            'type' => 'Type',
            'priority' => 'Priority',
            'is_active' => 'Is Active',
        ];
    }
}
