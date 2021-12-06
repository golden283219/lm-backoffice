<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_storage".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $url
 * @property int $quality
 * @property int $is_converted
 */
class MoviesStorage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_storage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie', 'url', 'quality', 'is_converted'], 'required'],
            [['id_movie', 'quality', 'is_converted'], 'integer'],
            [['url'], 'string', 'max' => 255],
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
            'url' => 'Url',
            'quality' => 'Quality',
            'is_converted' => 'Is Converted',
        ];
    }
}
