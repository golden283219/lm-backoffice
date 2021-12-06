<?php

namespace common\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "collection_data".
 *
 * @property int      $collection_data_id
 * @property int      $collection_id
 * @property int|null $imdb_id
 * @property int|null $movie_id
 * @property int|null $position
 */
class CollectionData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'collection_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['collection_id'], 'required'],
            [['collection_id', 'imdb_id', 'movie_id', 'position'], 'integer'],
            [['type'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'collection_data_id' => 'Collection Data ID',
            'collection_id'      => 'Collection ID',
            'imdb_id'            => 'Imdb ID',
            'movie_id'           => 'Movie ID',
            'type'               => 'Type',
            'position'           => 'Position',
        ];
    }
}
