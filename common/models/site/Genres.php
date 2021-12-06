<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "genres".
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string $display_title
 * @property int $display_filter
 * @property int $status
 */
class Genres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'genres';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['slug', 'title', 'display_title'], 'required'],
            [['display_filter', 'status'], 'integer'],
            [['slug', 'title', 'display_title'], 'string', 'max' => 255],
            [['slug'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Slug',
            'title' => 'Title',
            'display_title' => 'Display Title',
            'display_filter' => 'Display Filter',
            'status' => 'Status',
        ];
    }
}
