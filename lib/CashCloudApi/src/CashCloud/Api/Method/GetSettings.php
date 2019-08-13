<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class Settings
 * @package CashCloud\Api\Method
 */
class GetSettings extends Method
{
    /**
     * @var string
     */
    protected $callbackUrl;
    /**
     * @var string
     */
    protected $requestExpiration;

    /**
     * Return method URL
     *
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    public function getUrl(Client $api)
    {
        return $api->getMethodUrl("settings");
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
     * Return method data
     *
     * @return array
     */
    public function getData()
    {
        return array();
    }

    /**
     * Formats response
     *
     * @param Request $request
     * @return array
     */
    public function formatResponse(Request $request)
    {
        return array(
            $request->getJson('callback_url'),
            $request->getJson('request_expiration'),
        );
    }
}
