<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "movies_related".
 *
 * @property int $id
 * @property int $id_movie
 * @property int $related_id_movie
 */
class MoviesRelated extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_related';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie', 'related_id_movie'], 'required'],
            [['id_movie', 'related_id_movie'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_movie' => 'Id Movie',
            'related_id_movie' => 'Related Id Movie',
        ];
    }
}
