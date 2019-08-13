<?php namespace CashCloud\Api\Method;

use CashCloud\Api\Exception\AuthException;
use CashCloud\Api\Exception\CashCloudException;
use CashCloud\Api\Exception\ValidateException;
use CashCloud\Api\Rest\Client;
use CashCloud\Api\Rest\CurlRequest;
use CashCloud\Api\Rest\Request;

/**
 * Class Method
 * @package CashCloud\Method
 */
abstract class Method
{
    /**
     * @var \CashCloud\Api\Rest\Request
     */
    private $request;

    /**
     * Return method URL
     * @param \CashCloud\Api\Rest\Client $api
     * @return string
     */
    abstract public function getUrl(Client $api);

    /**
     * Return method data
     * @return array
     */
    abstract public function getData();

    /**
     * Construct method, depends on Request
     *
     * @param Request $request
     */
    function __construct(Request $request = null)
    {
        if(is_null($request)) {
            $this->request = new CurlRequest();
        } else {
            $this->request = $request;
        }
    }

    /**
     * Returns request method
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
     * @return array
     */
    abstract public function formatResponse(Request $request);

    /**
     * Performs the request
     *
     * @param Client $api
     * @return mixed
     * @throws \CashCloud\Api\Exception\ValidateException
     * @throws \CashCloud\Api\Exception\AuthException
     * @throws \CashCloud\Api\Exception\CashCloudException
     */
    public function perform(Client $api)
    {
        $this->request->setUrl($this->getUrl($api));
        $this->request->setData($this->getData());
        $this->request->setMethod($this->getMethod());

        $api->authorizeRequest($this->request);

        switch($this->request->execute()) {
            case 200:
                return $this->formatResponse($this->request);
            case 201:
                return $this->formatResponse($this->request);
            case 403:
                throw new AuthException();
            case 404:
                throw new CashCloudException("Invalid url", 404);
            case 412:
                throw new ValidateException($this->request->getJson("validationFailed"));
            case 423:
                throw new CashCloudException("API Locked!", 423);
            case 500:
                throw new CashCloudException("CashCloud server error", 500);
            case 0:
                throw new CashCloudException($this->request->getError(), $this->request->getErrorCode());
            default:
                throw new CashCloudException("Unknown exception", $this->request->getResponseCode());
        }
    }
}
