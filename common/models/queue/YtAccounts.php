<?php

namespace common\models\queue;

use Yii;

/**
 * This is the model class for table "yt_accounts".
 *
 * @property int $id
 * @property string $yt_login
 * @property string $yt_password
 * @property string $yt_recovery_email
 * @property int $in_use
 */
class YtAccounts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'yt_accounts';
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
            [['in_use'], 'integer'],
            [['yt_login', 'yt_password', 'yt_recovery_email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'yt_login' => 'Yt Login',
            'yt_password' => 'Yt Password',
            'yt_recovery_email' => 'Yt Recovery Email',
            'in_use' => 'In Use',
        ];
    }
}
