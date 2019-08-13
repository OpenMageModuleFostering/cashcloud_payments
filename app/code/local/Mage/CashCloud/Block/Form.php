<?php
/**
 * Class Mage_CashCloud_Block_Form
 *
 * @method Mage_CashCloud_Model_PaymentMethod getMethod()
 */
class Mage_CashCloud_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * @return int
     */
    public function getBasePrice()
    {
        return $this->getMethod()->getCart()->getQuote()->getGrandTotal();
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    public function getEur()
    {
        return Mage::getModel('directory/currency')->load("EUR");
    }

    public function isReasonSelected()
    {
        return (bool) $this->getMethod()->getConfigData('reason');
    }

    protected function _construct()
    {
        $this->setTemplate('cashcloud/form.phtml');
        parent::_construct();
    }

    public function isCCREnabled()
    {
        return (bool) $this->getMethod()->getConfigData('enable_ccr') && $this->getPriceInCCR() !== false;
    }

    public function getPriceInEur()
    {
        $eur = $this->getMethod()->getEur();
        $priceInEur = Mage::app()->getStore()->getCurrentCurrency()->convert($this->getBasePrice(), $eur);
        return $eur->format($priceInEur);
    }

    public function getPriceInCCR()
    {
        return $this->getCashCloudHelper()->getAmountInCCR($this->getBasePrice());
    }


    public function getReasons()
    {
        return $this->getMethod()->getHelper()->getReasons();
    }

    public function getConversationRate()
    {
        return Mage::app()->getStore()->getCurrentCurrency()->getRate($this->getEur());
    }

    /**
     * @return Mage_CashCloud_Helper_Data
     */
    private function getCashCloudHelper()
    {
        return Mage::helper('cashcloud');
    }
}
