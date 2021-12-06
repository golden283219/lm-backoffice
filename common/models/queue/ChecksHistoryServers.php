<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "checks_history_servers".
 *
 * @property int $id
 * @property int $checks_history_id
 * @property string $ip
 * @property string $server_name
 * @property string $status
 * @property string $message
 * @property string $yt_account
 * @property int $type
 */
class ChecksHistoryServers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'checks_history_servers';
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
            [['checks_history_id'], 'required'],
            [['checks_history_id', 'type'], 'integer'],
            [['ip'], 'string', 'max' => 100],
            [['server_name', 'message', 'yt_account'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'checks_history_id' => 'Checks History ID',
            'ip' => 'Ip',
            'server_name' => 'Server Name',
            'status' => 'Status',
            'message' => 'Message',
            'yt_account' => 'Yt Account',
            'type' => 'Type',
        ];
    }
}
