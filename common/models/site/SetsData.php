<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "sets_data".
 *
 * @property int $id
 * @property int $id_sets
 * @property string $title
 * @property string $url
 * @property string $cover
 * @property double $imdb_rating
 * @property int $year
 * @property int $id_movie
 * @property int $flag_quality
 */
class SetsData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sets_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_sets', 'title', 'url', 'cover', 'imdb_rating'], 'required'],
            [['id_sets', 'year', 'id_movie', 'flag_quality'], 'integer'],
            [['imdb_rating'], 'number'],
            [['title', 'url', 'cover'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sets' => 'Id Sets',
            'title' => 'Title',
            'url' => 'Url',
            'cover' => 'Cover',
            'imdb_rating' => 'Imdb Rating',
            'year' => 'Year',
            'id_movie' => 'Id Movie',
            'flag_quality' => 'Flag Quality',
        ];
    }
}
