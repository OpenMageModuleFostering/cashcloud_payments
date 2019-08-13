<?php
/**
 * File Checks.php
 *
 * @version GIT $Id$
 */
/**
 * Class Mage_CashCloud_Adminhtml_Model_Checks
 */
class Mage_CashCloud_Adminhtml_Model_Checks extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'cashcloud/checks.phtml';

    /**
     * Render fieldset html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $columns = ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='$columns'>" . $this->toHtml() . '</td>');
    }

    protected function _toHtml()
    {
        $this->setData(array(
            'username'=>Mage::getStoreConfig('payment/cashcloud/api_username'),
            'password'=>Mage::getStoreConfig('payment/cashcloud/api_password'),
            'device_id'=>Mage::getStoreConfig('payment/cashcloud/device_id'),
            'reason'=>Mage::getStoreConfig('payment/cashcloud/reason'),
        ));
        return parent::_toHtml();
    }

    /**
     * @return string
     */
    public function getCallbackUrl()
    {
        return Mage::getUrl("cashcloud/pay/callback");
    }
}
