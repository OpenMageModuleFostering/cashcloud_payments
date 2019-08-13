<?php
/**
 * Class Mage_CashCloud_Block_Form
 */
class Mage_CashCloud_Block_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('cashcloud/info.phtml');
    }

    /**
     * @return Mage_CashCloud_Model_PaymentMethod
     */
    public function getMethod()
    {
        return $this->getInfo()->getMethodInstance();
    }

    public function getFormattedPrice()
    {
        $grandTotalInEur = $this->getMethod()->getGrandTotalInEur(true);
        if($this->getInfo()->getAdditionalInformation("cashcloud_currency") == "CCR") {
            $amount =  $this->getMethod()->getHelper()->getAmountInCCR($grandTotalInEur);
            return number_format($amount, 2). " CCR";
        } else {
            return $grandTotalInEur;
        }
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->getInfo()->getAdditionalInformation('cashcloud_reason_id');
    }
}
