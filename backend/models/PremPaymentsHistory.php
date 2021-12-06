<?php


namespace backend\models;


class PremPaymentsHistory extends \common\models\PremPaymentsHistory
{
    CONST HISTORY_STATUS_REFUNDED = 3;
    const HISTORY_STATUS_ERROR    = 2;
    const HISTORY_STATUS_PAID     = 1;
    const HISTORY_STATUS_NOT_PAID = 0;

    const PAYMENT_METHOD_PAYPAL = 3;
    const PAYMENT_METHOD_PAYPAL_CC = 2;
    const PAYMENT_METHOD_CC = 1;

    public static $payment_method_vocabulary = [
        1 => 'Credit Card',
        2 => 'Paypal / Credit Card',
        3 => 'Paypal'
    ];

    public static $history_status_vocabulary = [
        1 => '<span class="badge badge-success">Paid</span>',
        0 => '<span class="badge">Not Paid</span>',
        2 => '<span class="badge badge-danger">Process Error</span>',
        3 => '<span class="badge badge-warning">Refunded</span>'
    ];

    public static function get_method_label($method_id)
    {
        return isset(self::$payment_method_vocabulary[$method_id]) ? self::$payment_method_vocabulary[$method_id] : '(Not Set)';
    }
}
