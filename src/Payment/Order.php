<?php namespace Yxd\Game\Payment;

use Yxd\Game\Support\Attribute;

/**
 * Class Order.
 *
 * @property string $open_id
 * @property string $money
 * @property string $game_order_no
 * @property string $title
 * @property string $description
 * @property string $notify_url
 */
class Order extends Attribute
{
    protected $attributes = [
        'open_id',
        'money',
        'game_order_no',
        'title',
        'description',
        'notify_url',
    ];
}
