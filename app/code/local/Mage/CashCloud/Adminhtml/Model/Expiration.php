<?php
/**
 * File Reason.php
 *
 * @version GIT $Id$
 */
/**
 * Class Reason
 */
class Mage_CashCloud_Adminhtml_Model_Expiration
{
    public function toOptionArray()
    {
        return array(
            array('value' => 60*2, 'label'=>Mage::helper('cashcloud')->__('2 minutes')),
            array('value' => 60*3, 'label'=>Mage::helper('adminhtml')->__('30 minutes')),
            array('value' => 60*60, 'label'=>Mage::helper('adminhtml')->__('1 hour')),
            array('value' => 60*60*2, 'label'=>Mage::helper('adminhtml')->__('2 hour')),
            array('value' => 60*60*6, 'label'=>Mage::helper('adminhtml')->__('6 hour')),
            array('value' => 60*60*12, 'label'=>Mage::helper('adminhtml')->__('12 hours')),
            array('value' => 60*60*24, 'label'=>Mage::helper('adminhtml')->__('24 hours')),
            array('value' => 60*60*24*2, 'label'=>Mage::helper('adminhtml')->__('2 days')),
            array('value' => 60*60*24*8, 'label'=>Mage::helper('adminhtml')->__('8 days')),
        );
    }

    /**
     * @return Mage_CashCloud_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper("cashcloud");
    }
}
