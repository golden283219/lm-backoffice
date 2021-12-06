<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "yt_converters".
 *
 * @property int $id
 * @property string $ip
 * @property string $server_name
 * @property string $status_check_url
 * @property int $type
 */
class YtConverters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yt_converters';
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
            [['type'], 'integer'],
            [['ip'], 'string', 'max' => 100],
            [['server_name', 'status_check_url'], 'string', 'max' => 255],
            [['ip', 'server_name', 'status_check_url', 'type'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'server_name' => 'Server Name',
            'status_check_url' => 'Status Check Url',
            'type' => 'Type',
        ];
    }
}
