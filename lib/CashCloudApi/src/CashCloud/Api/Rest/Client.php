<?php namespace CashCloud\Api\Rest;

use CashCloud\Api\Exception\AuthException;
use CashCloud\Api\Exception\CashCloudException;

/**
 * Class Client
 */
class Client
{
    const STATUS_PENDING_ACCEPT = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_DECLINED = 4;
    const STATUS_EXPIRED = 6;
    const STATUS_MONEY_SERVICE_FAILED = 7;
    const STATUS_REFUNDED = 8;

    const CURRENCY_EUR = 1;
    const CURRENCY_CCR = 2;

    const CURRENCY_BUY = 1;
    const CURRENCY_SELL = 2;
    const CURRENCY_RECEIVE = 3;

    /**
     * @var null|string
     */
    private $salt;
    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var string
     */
    private $apiUrlSandbox = 'https://merchant-api.sandbox.cashcloud.com/';

    /**
     * @var string
     */
    private $apiUrl = 'https://merchant-api.cashcloud.com/';

    /**
     * @var bool
     */
    private $sandbox = false;

    /**
     * @var string
     */
    private $backend = '\CashCloud\Api\Rest\CurlRequest';

    /**
     * Client Constructor
     *
     * @param Auth $authentication
     * @param string|null $salt
     * @param bool $sandbox
     */
    function __construct(Auth $authentication, $salt = null, $sandbox = false)
    {
        $this->auth = $authentication;
        $this->salt = $salt;
        $this->sandbox = $sandbox;
    }


    /**
     * Returns salt from api
     *
     * @param Request $request
     * @throws \CashCloud\Api\Exception\AuthException
     * @throws \CashCloud\Api\Exception\CashCloudException
     * @return mixed
     */
    public function getSalt(Request $request)
    {
        if (is_null($this->salt)) {
            $request->setUrl($this->getMethodUrl("salt"));
            $request->setMethod(Request::POST);
            $request->setData(array(
                'username' => $this->auth->getUsername(),
                'device_id' => $this->auth->getDeviceId(),
            ));

            if ($request->execute() == 200) {
                $response = $request->getJson();
                if (isset($response->salt)) {
                    return $response->salt;
                } else {
                    throw new CashCloudException("Invalid response from CashCloud: " . $request->getBody());
                }
            }

            if ($request->getErrorCode() > 0) {
                throw new CashCloudException($request->getError(), $request->getErrorCode());
            }

            if ($request->getResponseCode() != 200) {
                throw new AuthException("Unable to retrieve salt from CashCloud", $request->getResponseCode());
            }
        }
        return $this->salt;
    }

    /**
     * Authorizes request
     *
     * @param Request $request
     */
    public function authorizeRequest(Request $request)
    {
        $this->auth->authorizeRequest($this, $request);
    }

    /**
     * Returns method url
     *
     * @param string $method
     * @return string
     */
    public function getMethodUrl($method)
    {
        return ($this->sandbox ? $this->apiUrlSandbox : $this->apiUrl) . $method;
    }

    /**
     * Creates new request
     *
     * @return Request
     */
    public function createRequest()
    {
        return new $this->backend;
    }
}
