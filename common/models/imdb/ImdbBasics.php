<?php

namespace common\models\imdb;

use Yii;

/**
 * This is the model class for table "imdb_basics".
 *
 * @property int $id
 * @property string $tconst
 * @property string|null $title_type
 * @property string|null $primary_title
 * @property string|null $original_title
 * @property int|null $is_adult
 * @property int|null $start_year
 * @property int|null $end_year
 * @property int|null $runtime_minutes
 * @property string|null $genres
 */
class ImdbBasics extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_basics';
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
            [['id', 'is_adult', 'start_year', 'end_year', 'runtime_minutes'], 'integer'],
            [['tconst'], 'required'],
            [['genres'], 'safe'],
            [['tconst', 'title_type'], 'string', 'max' => 30],
            [['primary_title', 'original_title'], 'string', 'max' => 610],
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
            'title_type' => 'Title Type',
            'primary_title' => 'Primary Title',
            'original_title' => 'Original Title',
            'is_adult' => 'Is Adult',
            'start_year' => 'Start Year',
            'end_year' => 'End Year',
            'runtime_minutes' => 'Runtime Minutes',
            'genres' => 'Genres',
        ];
    }
}
