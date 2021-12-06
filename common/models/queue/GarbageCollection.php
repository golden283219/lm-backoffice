<?php

namespace common\models\queue;

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
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_queue');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['storage', 'path'], 'required'],
            [['date_added'], 'safe'],
            [['s1_status', 's2_status'], 'integer'],
            [['storage'], 'string', 'max' => 100],
            [['path'], 'string', 'max' => 500],
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
