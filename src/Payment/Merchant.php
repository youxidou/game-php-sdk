<?php namespace Yxd\Game\Payment;

use Yxd\Game\Support\Attribute;

/**
 * Class Merchant.
 *
 * @property string $app_key
 * @property string $app_secret
 */
class Merchant extends Attribute
{
    /**
     * @var array
     */
    protected $attributes = [
        'app_key',
        'app_secret',
    ];

    /**
     * Aliases of attributes.
     *
     * @var array
     */
    protected $aliases = [
        'app_key'     => 'app_key',
        'app_secret' => 'app_secret',
    ];
}
