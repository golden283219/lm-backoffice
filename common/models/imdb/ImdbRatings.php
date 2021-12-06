<?php

namespace common\models\imdb;

use Yii;

/**
 * This is the model class for table "imdb_ratings".
 *
 * @property int $id
 * @property string $tconst
 * @property float|null $averageRating
 * @property int|null $numVotes
 */
class ImdbRatings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_ratings';
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
            [['id', 'numVotes'], 'integer'],
            [['tconst'], 'required'],
            [['averageRating'], 'number'],
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
            'averageRating' => 'Average Rating',
            'numVotes' => 'Num Votes',
        ];
    }
}
