<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_featured".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $date_added
 */
class MoviesFeatured extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_featured';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie'], 'required'],
            [['id_movie', 'position'], 'integer'],
            [['date_added'], 'safe'],
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
            'position' => 'Position',
            'date_added' => 'Date Added',
        ];
    }
}
