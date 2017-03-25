<?php namespace Yxd\Game\User;

use Yxd\Game\Core\API;
use Yxd\Game\Core\Exceptions\FaultException;
use Yxd\Game\Foundation\Request;

/**
 * Class User.
 */
class User extends API
{
    protected $request;

    /**
     * Fetch a user.
     *
     * @return \Yxd\Game\Support\Collection
     * @throws FaultException
     */
    public function get()
    {
        return $this->getUserInfo($this->getToken());
    }

    /**
     * Get User token of playing game;
     *
     * @return string
     * @throws FaultException
     */
    private function getToken()
    {
        $request = $this->getRequest();

        if (!$request->isValid()) {
            throw new FaultException('Invalid request payloads.', 400);
        }

        if (($token = $request->getData()->get('token')) == '') {
            throw new FaultException('Invalid user token.', 400);
        }

        return (string)$token;
    }

    /**
     * Request Setter
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Request data setter;
     *
     * @param array $data
     *
     * @return \Yxd\Game\Support\Collection
     */
    public function setRequestData(array $data)
    {
        return $this->getRequest()->setData($data);
    }

    /**
     * Return Request instance.
     *
     * @return Request
     */
    public function getRequest()
    {
        if ($this->request) {
            return $this->request;
        }

        return new Request($this->config);
    }
}
