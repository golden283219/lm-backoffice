<?php

namespace common\models\imdb;

use Yii;

/**
 * This is the model class for table "imdb_name_basics".
 *
 * @property int $id
 * @property string $nconst
 * @property string|null $primaryName
 * @property string|null $birthYear
 * @property string|null $deathYear
 * @property string|null $primaryProfession
 * @property string|null $knownForTitles
 */
class ImdbNameBasics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_name_basics';
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
            [['nconst'], 'required'],
            [['primaryProfession', 'knownForTitles'], 'string'],
            [['nconst'], 'string', 'max' => 10],
            [['primaryName'], 'string', 'max' => 255],
            [['birthYear', 'deathYear'], 'string', 'max' => 4],
            [['nconst'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nconst' => 'Nconst',
            'primaryName' => 'Primary Name',
            'birthYear' => 'Birth Year',
            'deathYear' => 'Death Year',
            'primaryProfession' => 'Primary Profession',
            'knownForTitles' => 'Known For Titles',
        ];
    }
}
