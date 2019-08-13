<?php
/**
 * File Config.php
 *
 * @version GIT $Id$
 */
/**
 * Class Config
 */
class Mage_CashCloud_Adminhtml_Model_Config extends Mage_Core_Model_Config_Data
{
    public function __construct()
    {
        $this->getHelper()->init();
        parent::__construct();
    }


    protected function _afterSave()
    {
        if(!$this->getFieldsetDataValue("auto_set_settings")) {
            return parent::_afterSave();
        }

        try {
            $this->getHelper()->setCredentials(
                $this->getFieldsetDataValue("api_username"),
                $this->getFieldsetDataValue("api_password"),
                $this->getFieldsetDataValue("device_id"),
                $this->getFieldsetDataValue("enable_sandbox")
            );
            $this->getHelper()->setCallback(null, $this->getFieldsetDataValue("request_expiration"));
        } catch (\CashCloud\Api\Exception\AuthException $e) {
            Mage::throwException($this->getHelper()->__("Invalid cashcloud username or password"));
        } catch (\CashCloud\Api\Exception\ValidateException $e) {
            Mage::throwException($this->getHelper()->__($e->getFirstError()));
        } catch (\CashCloud\Api\Exception\CashCloudException $e) {
            Mage::throwException($this->getHelper()->__($e->getMessage()));
        }
        return parent::_afterSave();
    }

    /**
     * @return Mage_CashCloud_Helper_Data
     */
    protected function getHelper()
    {
        return Mage::helper('cashcloud');
    }
}
