<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class GetTransactions
 * @package CashCloud\Api\Method
 */
class GetTransactions extends Method
{
    /**
     * @var null
     */
    protected $hash = null;

    /**
     * Return method URL
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    public function getUrl(Client $api)
    {
        return $api->getMethodUrl("transactions");
    }

    /**
     * Returns cashcloud hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Return request method
     *
     * @return string
     */
    public function getMethod()
    {
        return Request::GET;
    }

    /**
     * Sets cashcloud hash
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * Return method data
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'hash' => $this->hash
        );
    }

    /**
     * Formats response
     *
     * @param Request $request
     * @return array
     */
    public function formatResponse(Request $request)
    {
        return $request->getJson();
    }
}