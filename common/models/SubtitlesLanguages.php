<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "subtitles_languages".
 *
 * @property int $id
 * @property string $language
 * @property string $isoCode
 */
class SubtitlesLanguages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subtitles_languages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['language', 'isoCode'], 'required'],
            [['language'], 'string', 'max' => 255],
            [['isoCode'], 'string', 'max' => 4],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'language' => 'Language',
            'isoCode' => 'Iso Code',
        ];
    }
}
