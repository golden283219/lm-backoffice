<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "collection_history".
 *
 * @property int $id
 * @property int $collection_id
 * @property string|null $started
 * @property string|null $finished
 * @property int|null $count_items
 *
 * @property Collection $collection
 */
class CollectionHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'collection_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['collection_id'], 'required'],
            [['collection_id', 'count_items'], 'integer'],
            [['started', 'finished'], 'safe'],
            [['collection_id'], 'exist', 'skipOnError' => true, 'targetClass' => Collection::className(), 'targetAttribute' => ['collection_id' => 'collection_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'collection_id' => 'Collection ID',
            'started' => 'Started',
            'finished' => 'Finished',
            'count_items' => 'Count Items',
        ];
    }

    /**
     * Gets query for [[Collection]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCollection()
    {
        return $this->hasOne(Collection::className(), ['collection_id' => 'collection_id']);
    }
}
