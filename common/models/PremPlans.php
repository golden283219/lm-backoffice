<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "prem_plans".
 *
 * @property int $id
 * @property double $price_usd
 * @property string $title
 * @property string $description
 * @property double $discount
 * @property string $code
 * @property int $extra_time
 * @property int $is_default
 * @property int $is_active
 * @property int $month_count
 * @property array $affiliate_tariff_maping
 */
class PremPlans extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prem_plans';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price_usd', 'discount'], 'number'],
            [['extra_time', 'is_default', 'is_active', 'month_count'], 'integer'],
            [['affiliate_tariff_maping'], 'safe'],
            [['title'], 'string', 'max' => 70],
            [['description'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'price_usd' => 'Price Usd',
            'title' => 'Title',
            'description' => 'Description',
            'discount' => 'Discount',
            'code' => 'Code',
            'extra_time' => 'Extra Time',
            'is_default' => 'Is Default',
            'is_active' => 'Is Active',
            'month_count' => 'Month Count',
            'affiliate_tariff_maping' => 'Affiliate Tariff Maping',
        ];
    }
}
