<?php
/**
 * Class Mage_CashCloud_PayController
 */
class Mage_CashCloud_PayController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function processAction()
    {
        $this->loadLayout();

        $order = $this->getCheckout()->getLastRealOrder();
        if(!$order) {
            Mage::throwException("Cannot find order");
        }

        $success = null;
        $orderStatus = $this->getMethodInstance()->getConfigData('order_status_after_payment');
        if($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING && $order->getStatus() == $orderStatus) {
            $success = true;
        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) {
            $success = false;
        }

        if(!$this->getRequest()->isAjax() && !is_null($success)) {
            $this->_redirect(
                $success ? 'checkout/onepage/success' : 'checkout/onepage/failure'
            );
        }

        if($this->getRequest()->isPost()) {
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array(
                'action'=> is_null($success) ? "wait" : "reload",
            )));
        } else {
            $this->getResponse()->setBody($this->getLayout()->createBlock('cashcloud/process')->toHtml());
        }
    }

    public function callbackAction()
    {
        $method = isset($_POST['method']) ? $_POST['method'] : false;
        if($method == "refund") {
            $this->getMethodInstance()->validateRefundCallback($_POST['hash']);
            exit();
        }
        $securityToken = isset($_POST['control']) ? $_POST['control'] : false;
        $time = isset($_POST['time']) ? $_POST['time'] : false;
        $status = isset($_POST['status']) ? $_POST['status'] : false;

        $payment = Mage::getSingleton("Mage_CashCloud_Model_PaymentMethod");

        $controlString = "$method|$status|$time|".md5(sha1($payment->getConfigData('api_password'), true), true);
        $realToken = base64_encode( md5( sha1( $controlString, true), true));

        if($securityToken != $realToken) {
            Mage::throwException("Not found");
        }

        $hash = isset($_POST['hash']) ? $_POST['hash'] : false;
        $order = $this->loadOrder($hash);

        if($order->getId()) {
            $callbackData = $this->getRequest()->getPost();
            $this->getMethodInstance()->validateCallback($order, $callbackData);
            echo "OK";
        } else {
            Mage::throwException($this->getHelper()->__("Order not found!"));
        }
    }

    /**
     * @return Mage_CashCloud_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper("cashcloud");
    }

    /**
     * @param $hash
     * @return Mage_Sales_Model_Order
     */
    public function loadOrder($hash)
    {
        /** @var Mage_Sales_Model_Order_Payment $orderPayment */
        $orderPayment = Mage::getSingleton("sales/order_payment")->load($hash, 'cashcloud_id');
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getSingleton("sales/order")->load($orderPayment->getParentId());
        if($order) {
            $orderPayment->setOrder($order);
            $order->setPayment($orderPayment);
        } else {
            Mage::throwException($this->getHelper()->__("Order not found!"));
        }
        return $order;
    }

    /**
     * @return Mage_CashCloud_Model_PaymentMethod
     */
    public function getMethodInstance()
    {
        return Mage::getSingleton("Mage_CashCloud_Model_PaymentMethod");
    }
}
