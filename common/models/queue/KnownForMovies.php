<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "known_for_movies".
 *
 * @property int $id
 * @property int $id_cast
 * @property int $id_movie
 *
 * @property CastImdb $cast
 * @property Movies $movie
 */
class KnownForMovies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'known_for_movies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cast', 'id_movie'], 'required'],
            [['id_cast', 'id_movie'], 'integer'],
            [['id_cast'], 'exist', 'skipOnError' => true, 'targetClass' => CastImdb::className(), 'targetAttribute' => ['id_cast' => 'id']],
            [['id_movie'], 'exist', 'skipOnError' => true, 'targetClass' => Movies::className(), 'targetAttribute' => ['id_movie' => 'id_movie']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_cast' => 'Id Cast',
            'id_movie' => 'Id Movie',
        ];
    }

    /**
     * Gets query for [[Cast]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCast()
    {
        return $this->hasOne(CastImdb::className(), ['id' => 'id_cast']);
    }

    /**
     * Gets query for [[Movie]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMovie()
    {
        return $this->hasOne(Movies::className(), ['id_movie' => 'id_movie']);
    }
}
