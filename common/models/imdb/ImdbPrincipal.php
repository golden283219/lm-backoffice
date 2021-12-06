<?php

namespace common\models\imdb;

use Yii;

/**
 * This is the model class for table "imdb_principal".
 *
 * @property int $id
 * @property string $tconst
 * @property int|null $ordering
 * @property string|null $nconst
 * @property string|null $category
 * @property string|null $job
 * @property string|null $characters
 */
class ImdbPrincipal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'imdb_principal';
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
            [['ordering'], 'integer'],
            [['tconst'], 'string', 'max' => 30],
            [['nconst', 'category', 'job', 'characters'], 'string', 'max' => 255],
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
            'ordering' => 'Ordering',
            'nconst' => 'Nconst',
            'category' => 'Category',
            'job' => 'Job',
            'characters' => 'Characters',
        ];
    }
}
