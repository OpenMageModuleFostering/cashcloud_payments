<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\Request;

/**
 * Class Settings
 * @package CashCloud\Api\Method
 */
class SaveSettings extends Method
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
        return Request::POST;
    }

    /**
     * Return method data
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'callback_url'=>$this->callbackUrl,
            'request_expiration'=>$this->requestExpiration,
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

    /**
     * Returns callback url
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Sets callback url
     *
     * @param string $callbackUrl
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
    }

    /**
     * Returns request expiration time
     *
     * @return string
     */
    public function getRequestExpiration()
    {
        return $this->requestExpiration;
    }

    /**
     * Sets request expiration time
     *
     * @param string $requestExpiration
     */
    public function setRequestExpiration($requestExpiration)
    {
        $this->requestExpiration = $requestExpiration;
    }
}
