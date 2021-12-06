<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "collection".
 *
 * @property int $collection_id
 * @property string $title
 * @property string $slug
 * @property string $url
 * @property string $type
 * @property string|null $description
 * @property int $is_active
 * @property string|null $last_data_update
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Collection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'collection';
    }


	public function beforeSave($insert)
	{
		if ($insert) {
			$this->created_at = date("Y-m-d H:i:s");
			$this->updated_at = date("Y-m-d H:i:s");
			return true;
		} else {
			$this->updated_at = date("Y-m-d H:i:s");
			return true;
		}

		return false;
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'slug', 'url', 'type'], 'required'],
            [['url', 'description'], 'string'],
            [['is_active', 'script_position'], 'integer'],
            [['last_data_update', 'created_at', 'updated_at'], 'safe'],
            [['title', 'slug', 'type'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'collection_id' => 'Collection ID',
            'title' => 'Title',
            'slug' => 'Slug',
            'url' => 'Url',
            'type' => 'Type',
            'description' => 'Description',
            'is_active' => 'Is Active',
            'script_position' => 'Position in script',
            'last_data_update' => 'Last Data Update',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getCollectionBySlug($slug) {
        return Collection::find()->where(['slug' => $slug])->one();
    }
}
