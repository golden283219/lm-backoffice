<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_genres".
 *
 * @property int $id
 * @property int $id_movie
 * @property int $id_genre
 */
class MoviesGenres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_genres';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie', 'id_genre'], 'required'],
            [['id_movie', 'id_genre'], 'integer'],
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
            'id_genre' => 'Id Genre',
        ];
    }
}
