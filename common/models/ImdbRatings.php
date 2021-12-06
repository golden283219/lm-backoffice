<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ratings".
 *
 * @property int $id
 * @property string $tconst
 * @property double $averageRating
 * @property int $numVotes
 */
class ImdbRatings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ratings';
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
            [['averageRating'], 'number'],
            [['numVotes'], 'integer'],
            [['tconst'], 'string', 'max' => 120],
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
