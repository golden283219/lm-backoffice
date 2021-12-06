<?php

namespace common\models\imdb;

use Yii;

/**
 * This is the model class for table "imdb_akas".
 *
 * @property int $id
 * @property string|null $titleId
 * @property int|null $ordering
 * @property string|null $title
 * @property string|null $region
 * @property string|null $language
 * @property string|null $types
 * @property string|null $attributes
 * @property int|null $isOriginalTitle
 */
class ImdbAkas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_akas';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_imdb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ordering', 'isOriginalTitle'], 'integer'],
            [['title'], 'string'],
            [['titleId', 'region', 'language', 'types', 'attributes'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titleId' => 'Title ID',
            'ordering' => 'Ordering',
            'title' => 'Title',
            'region' => 'Region',
            'language' => 'Language',
            'types' => 'Types',
            'attributes' => 'Attributes',
            'isOriginalTitle' => 'Is Original Title',
        ];
    }
}
