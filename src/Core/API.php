<?php namespace Yxd\Game\Core;

use Yxd\Game\Foundation\Config;
use Yxd\Game\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 * Class API.
 */
class API extends AbstractAPI
{
    // api
    const API_DOMAIN = 'http://www.yxd17.com/api/';
    const API_TEST_DOMAIN = 'http://192.168.104.53/api/';
    const USER_INFO_PATH = 'user/getUserInfo';

    /**
     * Config instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * API constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get user info;
     *
     * @param string $token
     *
     * @return \Yxd\Game\Support\Collection
     */
    public function getUserInfo($token)
    {
        return $this->request(self::USER_INFO_PATH, compact('token'), 'get');
    }

    /**
     * Config setter.
     *
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Config getter.
     *
     * @return Merchant
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Make a API request.
     *
     * @param string $api
     * @param array  $params
     * @param string $method
     * @param array  $options
     * @param bool   $returnResponse
     *
     * @return \Yxd\Game\Support\Collection|\Psr\Http\Message\ResponseInterface
     */
    public function request($path, array $params, $method = 'post', array $options = [], $returnResponse = false)
    {
        $api = self::API_DOMAIN . $path;
        if ($this->config->test == true) {
            $api = self::API_TEST_DOMAIN . $path;
        }
        $params['app_key'] = $this->config->app_key;
        $params['nonce'] = sha1(uniqid(mt_rand(1, 1000000), true));
        $params['timestamp'] = time();
        $params = array_filter($params);
        $params['signature'] = generate_sign($params, $this->config->app_secret, 'sha1');
        //$options['exceptions'] = false;
        $options = array_merge([
            'body'    => json_encode($params),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
        ], $options);
        $response = $this->getHttp()->request($api, $method, $options);

        return $returnResponse ? $response : $this->parseResponse($response);
    }

    /**
     * Parse Response body to array.
     *
     * @param ResponseInterface $response
     *
     * @return \Yxd\Game\Support\Collection
     */
    protected function parseResponse($response)
    {
        return new Collection($this->getHttp()->parseJSON($response));
    }
}
