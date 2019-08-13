<?php
/**
 * File Reason.php
 *
 * @version GIT $Id$
 */
/**
 * Class Reason
 */
class Mage_CashCloud_Adminhtml_Model_Reason
{
    public function toOptionArray()
    {
        return $this->getHelper()->getReasons();
    }

    /**
     * @return Mage_CashCloud_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper("cashcloud");
    }
}
