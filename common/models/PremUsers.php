<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "prem_users".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $last_login
 * @property string $avatar_path
 * @property int $cancel_timestamp
 * @property int $role
 * @property array $data
 * @property string $plain_password
 * @property string $latest_transaction_date
 */
class PremUsers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prem_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], 'required'],
            [['status', 'created_at', 'updated_at', 'last_login', 'cancel_timestamp', 'role'], 'integer'],
            [['data', 'latest_transaction_date'], 'safe'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'avatar_path'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['plain_password'], 'string', 'max' => 100],
            [['email'], 'unique'],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'last_login' => 'Last Login',
            'avatar_path' => 'Avatar Path',
            'cancel_timestamp' => 'Cancel Timestamp',
            'role' => 'Role',
            'data' => 'Data',
            'plain_password' => 'Plain Password',
            'latest_transaction_date' => 'Latest Transaction Date',
        ];
    }
}
