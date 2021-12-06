<?php

namespace common\models\imdb;

use Yii;

/**
 * This is the model class for table "imdb_crew".
 *
 * @property int $id
 * @property string $tconst
 * @property string|null $directors
 * @property string|null $writers
 */
class ImdbCrew extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_crew';
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
            [['tconst'], 'required'],
            [['directors', 'writers'], 'string'],
            [['tconst'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tconst' => 'Tconst',
            'directors' => 'Directors',
            'writers' => 'Writers',
        ];
    }
}
