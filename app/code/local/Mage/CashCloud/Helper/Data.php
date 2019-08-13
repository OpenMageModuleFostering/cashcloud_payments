<?php
use CashCloud\Api\Exception\CashCloudException;

/**
 * Class Api
 */
class Mage_CashCloud_Helper_Data extends Mage_Core_Helper_Data
{
    protected $username;
    protected $password;
    protected $deviceId;
    protected $sandboxMode = false;

    /**
     * @var \CashCloud\Api\Rest\Client
     */
    private $client = null;

    function __construct()
    {
        $this->init();
    }

    public function init()
    {
        // setup autoload
        require_once Mage::getBaseDir("lib") . DS . "CashCloudApi" . DS . "vendor" . DS . "autoload.php";

        /** @var Mage_CashCloud_Model_PaymentMethod $payment */
        $payment = Mage::getSingleton("Mage_CashCloud_Model_PaymentMethod");
        $this->username = $payment->getConfigData('api_username');
        $this->password = $payment->getConfigData('api_password');
        $this->deviceId = $payment->getConfigData('api_device_id');
        $this->sandboxMode = $payment->getConfigData('enable_sandbox');
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $deviceId
     * @param bool $sandboxMode
     */
    public function setCredentials($username, $password, $deviceId, $sandboxMode = false)
    {
        $this->username = $username;
        $this->password = $password;
        $this->deviceId = $deviceId;
        $this->sandboxMode = $sandboxMode;
    }

    /**
     * @return \CashCloud\Api\Rest\Client
     */
    public function getClient()
    {
        if (is_null($this->client)) {
            // create client
            $this->client = new \CashCloud\Api\Rest\Client(
                new \CashCloud\Api\Rest\Auth($this->username, $this->password, $this->deviceId), null, $this->sandboxMode
            );
        }
        return $this->client;
    }

    /**
     * @return array
     */
    public function getReasons()
    {
        $reasons = $this->getCached("cashcloud/reason_ids");
        if ($reasons != false) {
            return $reasons;
        }

        try {
            $req = new \CashCloud\Api\Method\GetReasons();
            $reasons = $req->perform($this->getClient());
        } catch (CashCloudException $e) {
            $reasons = array();
        }

        return $this->cache($reasons, "cashcloud/reason_ids");
    }

    /**
     * @param Mage_Sales_Model_Order_Payment $payment
     * @return string order ID
     */
    public function requestMoney($payment)
    {
        $order = $payment->getOrder();
        $method = Mage::getSingleton("Mage_CashCloud_Model_PaymentMethod");

        $request = new \CashCloud\Api\Method\RequestMoney();
        $request->setEmail($order->getPayment()->getAdditionalInformation('cashcloud_username'));
        $request->setAmount($order->getGrandTotal() * 100);
        if($order->getPayment()->getAdditionalInformation('cashcloud_currency') == "CCR") {
            $request->setCurrency(\CashCloud\Api\Rest\Client::CURRENCY_CCR);
        } else {
            $request->setCurrency(\CashCloud\Api\Rest\Client::CURRENCY_EUR);
        }
        $request->setExternalData($order->getId());
        if ($method->getConfigData('reason') == "") {
            Mage::throwException($this->__("Payments category not selected"));
        }
        $request->setReason($method->getConfigData('reason'));

        $response = $request->perform($this->getClient());
        return $response->hash;
    }

    /**
     * @param float $amount
     * @return false|float
     */
    public function getAmountInCCR($amount)
    {
        try {
            $request = new \CashCloud\Api\Method\GetRates();
            $request->setAmount($amount * 100); //cents
            $rate = $request->perform($this->getClient());

            $currencyId = \CashCloud\Api\Rest\Client::CURRENCY_CCR;
            $actionId = \CashCloud\Api\Rest\Client::CURRENCY_RECEIVE;
            if(isset($rate->{$currencyId}->{$actionId})) {
                // cents
                return (float) ($rate->{$currencyId}->{$actionId});
            }
        } catch (CashCloudException $e) {
            Mage::logException($e);
        }
        return false;
    }

    /**
     * @param $url
     * @param $request_expiration
     * @return bool|float
     */
    public function setCallback($url = null, $request_expiration = null)
    {
        $url = is_null($url) ? $this->getCallbackUrl() : $url;
        $request = new \CashCloud\Api\Method\SaveSettings();
        $request->setCallbackUrl($url);
        $request->setRequestExpiration($request_expiration);
        $result = $request->perform($this->getClient()) / 100;
        return $result;
    }

    public function refund($transactionId, $amount, $remark = "no remark")
    {
        try {
            $order = $this->loadOrderByHash($transactionId);
            $refund = new \CashCloud\Api\Method\Refund();
            $refund->setHash($transactionId);
            $refund->setAmount($amount * 100);
            $refund->setExternalData($order->getIncrementId());
            $refund->setRemark($remark);
            $refund->perform($this->getClient());
        } catch (\CashCloud\Api\Exception\CashCloudException $e) {
            Mage::throwException("Could not refund: " . $e->getMessage());
        }

        return $this;
    }
    /**
     * @param $hash
     * @return Mage_Sales_Model_Order
     */
    public function loadOrderByHash($hash)
    {
        /** @var Mage_Sales_Model_Order_Payment $orderPayment */
        $orderPayment = Mage::getSingleton("sales/order_payment")->load($hash, 'cashcloud_id');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getSingleton("sales/order")->load($orderPayment->getParentId());
        if($order)
        {

        } else {
            Mage::throwException($this->getHelper()->__("Order not found!"));
        }
        return $order;
    }
    public function getTransactionDataFromHash($hash)
	{
        try{
            $request = new \CashCloud\Api\Method\GetTransactions();
            $request->setHash($hash);
            $result = $request->perform($this->getClient());
            return $result->transaction;
        } catch (\CashCloud\Api\Exception\CashCloudException $e) {

        }
        return false;
	}
    /**
     * @param string $key
     * @return false|mixed
     */
    private function getCached($key)
    {
        $data = Mage::app()->getCache()->load($key);
        if ($data) {
            return unserialize(base64_decode($data));
        }
        return false;
    }

    /**
     * @param mixed $data
     * @param string $key
     * @return mixed
     */
    private function cache($data, $key)
    {
        Mage::app()->getCache()->save(base64_encode(serialize($data)), $key, array('cashcloud'), 60 * 60 * 3);
        return $data;
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return Mage::getUrl("cashcloud/pay/callback");
    }
}
