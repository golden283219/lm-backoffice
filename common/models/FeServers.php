<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "fe_servers".
 *
 * @property int $id
 * @property string $ip
 * @property string $server_name
 * @property string $status_check_url
 * @property int $max_bw
 * @property int $is_enabled
 * @property string $created_at
 * @property string $updated_at
 * @property array $domain_mapped
 * @property int $is_hidden
 */
class FeServers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fe_servers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip', 'server_name', 'status_check_url', 'max_bw'], 'required'],
            [['max_bw', 'is_enabled', 'is_hidden'], 'integer'],
            [['created_at', 'updated_at', 'domain_mapped'], 'safe'],
            [['ip', 'server_name'], 'string', 'max' => 80],
            [['status_check_url'], 'string', 'max' => 150],
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
            'max_bw' => 'Max Bw',
            'is_enabled' => 'Is Enabled',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'domain_mapped' => 'Domain Mapped',
            'is_hidden' => 'Is Hidden',
        ];
    }
}
