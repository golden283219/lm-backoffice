<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "home_page_data".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $code
 * @property string|null $icon
 * @property string|null $section_background
 * @property int|0 $position
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class HomePage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'home_page_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'code', 'view_type'], 'required'],
            [['title', 'code', 'icon', 'section_background'], 'string', 'max' => 255],
            [['position', 'collection_id', 'for_premium_user', 'is_active'], 'integer'],
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
            'code' => 'Code',
            'icon' => 'Icon',
            'section_background' => 'Section Background',
            'position' => 'Position',
            'collection_id' => 'Collection ID',
            'for_premium_user' => 'For Premium User',
            'is_active' => 'Is Active',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
