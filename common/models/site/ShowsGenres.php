<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "shows_genres".
 *
 * @property int $id
 * @property int $id_show
 * @property int $id_genre
 */
class ShowsGenres extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_genres';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_show', 'id_genre'], 'required'],
            [['id_show', 'id_genre'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_show' => 'Id Show',
            'id_genre' => 'Id Genre',
        ];
    }
}
