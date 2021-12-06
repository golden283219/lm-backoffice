<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "static_pages".
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $contents
 * @property string $created_at
 */
class StaticPages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'static_pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contents'], 'string'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 100],
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
            'slug' => 'Slug',
            'contents' => 'Contents',
            'created_at' => 'Created At',
        ];
    }
}
