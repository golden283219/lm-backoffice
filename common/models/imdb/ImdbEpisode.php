<?php

namespace common\models\imdb;

use Yii;

/**
 * This is the model class for table "imdb_episode".
 *
 * @property int $id
 * @property string $tconst
 * @property string $parentTconst
 * @property int|null $seasonNumber
 * @property int|null $episodeNumber
 */
class ImdbEpisode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_episode';
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
            [['tconst', 'parentTconst'], 'required'],
            [['seasonNumber', 'episodeNumber'], 'integer'],
            [['tconst', 'parentTconst'], 'string', 'max' => 30],
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
            'parentTconst' => 'Parent Tconst',
            'seasonNumber' => 'Season Number',
            'episodeNumber' => 'Episode Number',
        ];
    }
}
