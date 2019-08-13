<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Exception\CashCloudException;
use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class GetReasons
 * @package CashCloud\Api\Method
 */
class GetReasons extends Method
{

    /**
     * Return method URL
     *
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    public function getUrl(Client $api)
    {
        return $api->getMethodUrl('reasons');
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
     * Return request method
     *
     * @return string
     */
    public function getMethod()
    {
        return Request::GET;
    }


    /**
     * Formats response
     *
     * @param Request $request
     * @throws \CashCloud\Api\Exception\CashCloudException
     * @return array
     */
    public function formatResponse(Request $request)
    {
        $response = json_decode($request->getBody(), true);

        if(isset($response['messages']) && is_array($response['messages'])) {
            return $response['messages'];
        }

        throw new CashCloudException("Unexpected response from API:".$request->getBody());
    }
}
