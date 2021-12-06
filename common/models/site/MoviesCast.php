<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "movies_cast".
 *
 * @property int $id
 * @property string $role
 * @property int $id_cast
 * @property int $id_movie
 * @property string|null $hero
 */
class MoviesCast extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movies_cast';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cast', 'id_movie'], 'required'],
            [['id_cast', 'id_movie'], 'integer'],
            [['role'], 'string', 'max' => 20],
            [['hero'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'id_cast' => 'Id Cast',
            'id_movie' => 'Id Movie',
            'hero' => 'Hero',
        ];
    }
}
