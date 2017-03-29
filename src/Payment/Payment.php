<?php namespace Yxd\Game\Payment;

use Yxd\Game\Core\API;
use Yxd\Game\Core\Exceptions\FaultException;
use Yxd\Game\Foundation\Config;
use Yxd\Game\Foundation\Request;

/**
 * Class Payment.
 *
 * @mixin API
 */
class Payment
{
    const API_PREPARE_ORDER = 'pay/unified/order';

    /**
     * @var API
     */
    protected $api;

    /**
     * config instance.
     *
     * @var \Yxd\Game\Foundation\Config
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
     * Prepare order to pay.
     *
     * @param Order $order
     *
     * @return \Yxd\Game\Support\Collection
     * @throws FaultException
     */
    public function prepare(Order $order)
    {
        $order->notify_url = $order->get('notify_url', $this->config->notify_url);

        $data = $this->request(self::API_PREPARE_ORDER, $order->all());
        if ($data->result_code == 'SUCCESS') {
            if (!(new Request($this->getConfig()))->setData($data->toArray())->isValid()) {
                throw new FaultException('Invalid request payloads.', 400);
            }
        }

        return $data;
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

        $notify = $notify->getData();
        $successful = $notify->get('trade_result') === 'SUCCESS';

        $handleResult = call_user_func_array($callback, [$notify, $successful]);

        if ($handleResult === true) {
            return 'SUCCESS';
        }

        return 'FAIL';
    }

    /**
     * Generate js config for pay.
     *
     *
     * @param string $prepay_id
     * @param bool   $json
     *
     * @return string|array
     */
    public function configForPay($prepay_id, $json = true)
    {
        $params = [
            'app_key'   => $this->config->app_key,
            'timestamp' => strval(time()),
            'nonce'     => sha_nonce(),
            'prepay_id' => $prepay_id,
        ];

        $params['signature'] = generate_sign($params, $this->config->app_secret, 'sha1');

        return $json ? json_encode($params) : $params;
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
            return call_user_func_array([$this->getAPI(), $method], $args);
        }
    }
}
