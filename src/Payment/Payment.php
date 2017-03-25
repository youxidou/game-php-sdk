<?php namespace Yxd\Game\Payment;

use GuzzleHttp\Psr7\Request;
use Yxd\Game\Core\API;
use Yxd\Game\Core\Exceptions\FaultException;

/**
 * Class Payment.
 *
 * @mixin API
 */
class Payment
{

    /**
     * @var API
     */
    protected $api;

    /**
     * config instance.
     *
     * @var \Yxd\Game\Support\Config
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param Merchant $merchant
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Handle payment notify.
     *
     * @param callable $callback
     *
     * @return string
     */
    public function handleNotify(callable $callback)
    {
        $notify = $this->getNotify();

        if (!$notify->isValid()) {
            throw new FaultException('Invalid request payloads.', 400);
        }

        $notify = $notify->getNotify();
        $successful = $notify->get('result_code') === 'SUCCESS';

        $handleResult = call_user_func_array($callback, [$notify, $successful]);

        if ($handleResult === true) {
            return 'SUCCESS';
        }

        return 'FAIL';
    }


    /**
     * [JSSDK] Generate js config for payment.
     *
     * <pre>
     * wx.chooseWXPay({...});
     * </pre>
     *
     * @param string $prepayId
     *
     * @return array|string
     */
    public function configForJSSDKPayment($prepayId)
    {
        $config = $this->configForPayment($prepayId, false);

        $config['timestamp'] = $config['timeStamp'];
        unset($config['timeStamp']);

        return $config;
    }

    /**
     * Return Notify instance.
     *
     * @return Request
     */
    public function getNotify()
    {
        return new Request($this->getConfig());
    }

    /**
     * API setter.
     *
     * @param API $api
     *
     * @return API
     */
    public function setAPI(API $api)
    {
        return $this->api = $api;
    }

    /**
     * Return API instance.
     *
     * @return API
     */
    public function getAPI()
    {
        return $this->api ?: $this->setAPI(new API($this->getConfig()));
    }

    /**
     * Return Config instance.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Config setter
     *
     * @param Config $config
     *
     * @return Config
     */
    public function setConfig(Config $config)
    {
        return $this->config = $config;
    }

    /**
     * Magic call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->getAPI(), $method])) {
            return call_user_func_array([$this->api, $method], $args);
        }
    }
}
