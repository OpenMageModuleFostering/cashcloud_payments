<?php


/**
 * Class PaymentMethod
 */
class Mage_CashCloud_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'cashcloud';
    protected $_formBlockType = 'cashCloud/form';
    protected $_infoBlockType = 'cashCloud/info';

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * Availability options
     */
    protected $_canCapture = true;
    protected $_isGateway = false;

    /**
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setTransactionId($payment->getCashcloudId())->setIsTransactionClosed(0);

        return $this;
    }


    /**
     * @param Varien_Object $payment
     * @param float $amount
     * @return $this
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $this->getHelper()->refund($payment->getParentTransactionId(), $amount);
        return $this;
    }


    /**
     * @param null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (Mage::app()->getStore()->getCurrentCurrencyCode() == "EUR") {
            return parent::isAvailable($quote);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getGrandTotal()
    {
        return $this->getCart()->getQuote()->getGrandTotal();
    }

    /**
     * @return Mage_Directory_Model_Currency
     */
    public function getEur()
    {
        return Mage::getModel('directory/currency')->load("EUR");
    }

    /**
     * @param bool $raw
     * @return int
     */
    public function getGrandTotalInEur($raw = false)
    {
        $priceInEur = Mage::app()->getStore()->getCurrentCurrency()->convert($this->getGrandTotal(), $this->getEur());
        return $raw ? $priceInEur : $this->getEur()->format($priceInEur);
    }

    /**
     * @return $this
     */
    public function validate()
    {
        $info = $this->getInfoInstance();

        $username = $info->getAdditionalInformation("cashcloud_username");
        if (!preg_match('/([\w-+\.]+)@((?:[\w]+\.)+)([a-zA-Z]{2,4})/', $username)) {
            Mage::throwException(
                $this->_getHelper()->__('Invalid CashCloud username')
            );
        }

        $currency = $info->getAdditionalInformation("cashcloud_currency");
        if ($this->getConfigData('enable_ccr') && $currency != "EUR" && $currency != "CCR") {
            Mage::throwException(
                $this->_getHelper()->__('Please select currency')
            );
        }

        if (!$this->getConfigData('reason')) {
            $reason = $info->getAdditionalInformation("cashcloud_reason_id");
            if (empty($reason) || !array_key_exists($reason, $this->getHelper()->getReasons())) {
                Mage::throwException(
                    $this->_getHelper()->__('Please select purchase reason')
                );
            }
        }

        return parent::validate();
    }

    /**
     * @param mixed $data
     * @return $this|Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        parent::assignData($data);

        $currency = isset($_POST['payment']['cashcloud_currency']) ? $_POST['payment']['cashcloud_currency'] : "EUR";
        $username = isset($_POST['payment']['cashcloud_username']) ? $_POST['payment']['cashcloud_username'] : false;
        $reason = isset($_POST['payment']['cashcloud_reason_id']) ? $_POST['payment']['cashcloud_reason_id'] : false;

        $this->getInfoInstance()->setAdditionalInformation(array(
            'cashcloud_username' => $username,
            'cashcloud_currency' => $currency,
            'cashcloud_reason_id' => $reason,
        ));
        return $this;
    }


    /**
     * @return Mage_Checkout_Model_Cart
     */
    public function getCart()
    {
        return Mage::helper('checkout/cart');
    }

    /**
     * @return Mage_CashCloud_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper("cashcloud");
    }

    /**
     * Payment action getter compatible with payment model
     *
     * @see Mage_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return "order";
    }


    public function order(Varien_Object $payment, $amount)
    {
        $this->getHelper()->init();
        try {
            $orderId = $this->getHelper()->requestMoney($payment);
            $payment->setCashcloudId($orderId);
            $payment->save();
        }  catch (\CashCloud\Api\Exception\ValidateException $e) {
            Mage::throwException(
                $this->_getHelper()->__("Validation failed").": ".$this->_getHelper()->__($e->getFirstError())
            );
        } catch (\CashCloud\Api\Exception\AuthException $e) {
            Mage::throwException(
                $this->_getHelper()->__("Error while authorizing payment. Please check CashCloud account details in configuration!")
            );
        } catch (\CashCloud\Api\Exception\CashCloudException $e) {
            Mage::logException($e);
            Mage::throwException(
                $this->_getHelper()->__("Error while executing payment in CashCloud!")
            );
        }

        return $this;
    }

    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('cashcloud/pay/process');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param array $callbackData
     */
    public function validateCallback($order, $callbackData)
    {
        $this->getHelper()->init();
        $status = isset($callbackData['status']) ? $callbackData['status'] : null;
        $unsuccessfulStatuses = array(
            \CashCloud\Api\Rest\Client::STATUS_EXPIRED,
            \CashCloud\Api\Rest\Client::STATUS_REFUNDED,
            \CashCloud\Api\Rest\Client::STATUS_DECLINED,
            \CashCloud\Api\Rest\Client::STATUS_MONEY_SERVICE_FAILED
        );

        if($status == \CashCloud\Api\Rest\Client::STATUS_COMPLETED) {
            $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
            $orderStatus = $this->getConfigData('order_status_after_payment');
            $this->saveStatus($order, $orderState, $orderStatus, $this->getHelper()->__("Payment notification received"));
        } elseif(in_array($status, $unsuccessfulStatuses)) {
            $orderState = Mage_Sales_Model_Order::STATE_CANCELED;
            $orderStatus = Mage_Sales_Model_Order::STATE_CANCELED;
            $this->saveStatus($order, $orderState, $orderStatus, $this->getHelper()->__("Customer cancelled order"));
        } else {
            Mage::throwException("Invalid cashcloud status: $status");
        }
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @param string $orderState
     * @param string $orderStatus
     * @param string $comment
     */
    protected function saveStatus($order, $orderState, $orderStatus, $comment){
        $order->setState($orderState, $orderStatus, $comment)->save();

        /** @var Mage_Sales_Model_Order_Payment $payment */
        $payment = $order->getPaymentsCollection()->getFirstItem();

        if($orderStatus == Mage_Sales_Model_Order::STATE_PROCESSING) {
            $order->addStatusHistoryComment($this->getHelper()->__("Payment received"), $orderStatus);
        } else {

        }


//        var_dump($order->getData(), $payment->getData()); die();
        /** @var Mage_Sales_Model_Order_Status_History $comment */
//
//
//        $comment->save();
    }
}
