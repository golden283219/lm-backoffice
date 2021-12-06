<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_actors".
 *
 * @property int $id
 * @property int $id_actor
 * @property int $id_movie
 * @property string $hero
 */
class MoviesActors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_actors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_actor', 'id_movie', 'hero'], 'required'],
            [['id_actor', 'id_movie'], 'integer'],
            [['hero'], 'string', 'max' => 120],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_actor' => 'Id Actor',
            'id_movie' => 'Id Movie',
            'hero' => 'Hero',
        ];
    }
}
