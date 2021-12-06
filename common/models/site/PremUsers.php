<?php

namespace common\models\site;

use Yii;

/**
 * This is the model class for table "prem_users".
 *
 * @property int $id
 * @property string|null $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property string|null $email_confirm_token
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $last_login
 * @property string|null $avatar_path
 * @property int|null $cancel_timestamp
 * @property int|null $role
 * @property string|null $data
 * @property string|null $plain_password
 * @property string $latest_transaction_date
 * @property string|null $token_key
 * @property int|null $allowance
 * @property int|null $allowance_updated_at
 * @property string|null $conc_login
 *
 * @property ShowsNotifications[] $showsNotifications
 */
class PremUsers extends \yii\db\ActiveRecord
{
    const ACTIVE = 1;
    const DELETED = 0;
	const WAITING = 5; // User registered but not confirmed email address yet

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
            [['status', 'created_at', 'updated_at', 'last_login', 'cancel_timestamp', 'role', 'allowance', 'allowance_updated_at'], 'integer'],
            [['data', 'latest_transaction_date'], 'safe'],
            [['conc_login'], 'string'],
            [['username', 'password_hash', 'password_reset_token', 'email', 'email_confirm_token', 'avatar_path'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['plain_password', 'token_key'], 'string', 'max' => 100],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['token_key'], 'unique'],
            [['email_confirm_token'], 'unique'],
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
            'email_confirm_token' => 'Email Confirm Token',
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
            'token_key' => 'Token Key',
            'allowance' => 'Allowance',
            'allowance_updated_at' => 'Allowance Updated At',
            'conc_login' => 'Conc Login',
        ];
    }

    /**
     * Gets query for [[ShowsNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShowsNotifications()
    {
        return $this->hasMany(ShowsNotifications::className(), ['user_id' => 'id']);
    }
}
