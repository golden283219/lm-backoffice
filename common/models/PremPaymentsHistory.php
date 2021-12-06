<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "prem_payments_history".
 *
 * @property int $id
 * @property int $id_prem_plan
 * @property int $id_prem_user
 * @property double $paid_usd
 * @property int $paid_at
 * @property int $created_at
 * @property int $payment_status
 * @property string $guid
 * @property string $title
 * @property array $order_payload
 * @property string $payment_process_url
 * @property string $order_email
 * @property int $payment_method
 */
class PremPaymentsHistory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prem_payments_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_prem_plan', 'id_prem_user', 'created_at', 'payment_status', 'payment_method'], 'integer'],
            [['paid_usd'], 'number'],
            [['order_payload', 'paid_at'], 'safe'],
            [['guid'], 'string', 'max' => 40],
            [['title'], 'string', 'max' => 120],
            [['payment_process_url', 'order_email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_prem_plan' => 'Id Prem Plan',
            'id_prem_user' => 'Id Prem User',
            'paid_usd' => 'Paid Usd',
            'paid_at' => 'Paid At',
            'created_at' => 'Created At',
            'payment_status' => 'Payment Status',
            'guid' => 'Guid',
            'title' => 'Title',
            'order_payload' => 'Order Payload',
            'payment_process_url' => 'Payment Process Url',
            'order_email' => 'Order Email',
            'payment_method' => 'Payment Method',
        ];
    }
}
