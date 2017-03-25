<?php namespace Yxd\Game\Payment;

use Yxd\Game\Support\Attribute;

/**
 * Class Order.
 *
 * @property string $body
 * @property string $detail
 * @property string $attach
 * @property string $out_trade_no
 * @property string $fee_type
 * @property string $total_fee
 * @property string $spbill_create_ip
 * @property string $time_start
 * @property string $time_expire
 * @property string $goods_tag
 * @property string $notify_url
 * @property string $trade_type
 * @property string $product_id
 * @property string $limit_pay
 * @property string $openid
 * @property string $sub_openid
 * @property string $auth_code
 */
class Order extends Attribute
{
    const JSAPI = 'JSAPI';
    const NATIVE = 'NATIVE';
    const APP = 'APP';
    const MICROPAY = 'MICROPAY';

    protected $attributes = [
        'open_id',
        'money',
        'game_order_no',
        'title',
        'description',
        'notify_url',


        'sub_openid',
        'auth_code',
    ];
}
