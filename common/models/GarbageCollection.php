<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "garbage_collection".
 *
 * @property int $id
 * @property string $storage
 * @property string $path
 * @property string $date_added
 * @property int $s1_status
 * @property int $s2_status
 */
class GarbageCollection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'garbage_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 's1_status', 's2_status'], 'integer'],
            [['storage', 'path'], 'required'],
            [['date_added'], 'safe'],
            [['storage'], 'string', 'max' => 100],
            [['path'], 'string', 'max' => 500],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'storage' => 'Storage',
            'path' => 'Path',
            'date_added' => 'Date Added',
            's1_status' => 'S1 Status',
            's2_status' => 'S2 Status',
        ];
    }
}
