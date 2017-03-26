<?php namespace Yxd\Game\Foundation;

use Yxd\Game\Support\Collection;

/**
 * Class Request.
 */
class Request
{
    /**
     * Game config
     *
     * @var Config
     */
    protected $config;

    /**
     * Request data.
     *
     * @var Collection
     */
    protected $data;

    /**
     * Constructor.
     *
     * @param Config $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Validate the request params.
     *
     * @return bool
     */
    public function isValid()
    {
        $localSign = generate_sign($this->getData()->except('signature')->all(), $this->config->app_secret, 'sha1');

        return $localSign === $this->getData()->get('signature');
    }

    /**
     * Return the request body from POST.
     *
     * @return \Yxd\Game\Support\Collection
     *
     */
    public function getData()
    {
        if (!empty($this->data)) {
            return $this->data;
        }

        $data = array_merge($_POST, $_GET);

        return $this->setData($data);
    }

    /**
     * Set Request Data.
     *
     * @param $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = new Collection((array)$data);

        return $this;
    }
}
