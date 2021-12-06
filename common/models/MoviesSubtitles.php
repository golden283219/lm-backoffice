<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "movies_subtitles".
 *
 * @property int $id
 * @property int $id_movie
 * @property string $url
 * @property string $language
 * @property int $is_approved
 * @property int $is_moderated
 *
 */
class MoviesSubtitles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_subtitles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_movie', 'url', 'language'], 'required'],
            [['id_movie', 'is_approved', 'is_moderated'], 'integer'],
            [['url'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 120],
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
            'language' => 'Language',
            'is_approved' => 'Is Approved',
            'is_moderated' => 'Is Moderated',
        ];
    }
}
