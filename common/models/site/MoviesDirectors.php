<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_directors".
 *
 * @property int $id
 * @property int $id_director
 * @property int $id_movie
 */
class MoviesDirectors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_directors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_director', 'id_movie'], 'required'],
            [['id_director', 'id_movie'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_director' => 'Id Director',
            'id_movie' => 'Id Movie',
        ];
    }
}
