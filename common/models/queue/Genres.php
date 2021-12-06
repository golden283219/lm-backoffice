<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "genres".
 *
 * @property integer $id
 * @property string $title
 */
class Genres extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'genres';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function getAll()
    {
        return Genres::find()
            ->orderBy(['title' => SORT_ASC])
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
        ];
    }
}
