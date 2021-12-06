<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "shows_cast".
 *
 * @property int $id
 * @property int $id_cast
 * @property int $id_show
 * @property string $role
 * @property string|null $hero
 */
class ShowsCast extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shows_cast';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_cast', 'id_show', 'role'], 'required'],
            [['id_cast', 'id_show'], 'integer'],
            [['role'], 'string', 'max' => 20],
            [['hero'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_cast' => 'Id Cast',
            'id_show' => 'Id Show',
            'role' => 'Role',
            'hero' => 'Hero',
        ];
    }
}
