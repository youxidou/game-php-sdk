<?php namespace Yxd\Game\Foundation\ServiceProviders;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Yxd\Game\Payment\Payment;

/**
 * Class PaymentServiceProvider.
 */
class PaymentServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['payment'] = function ($pimple) {
            return new Payment($pimple['config']);
        };
    }
}
