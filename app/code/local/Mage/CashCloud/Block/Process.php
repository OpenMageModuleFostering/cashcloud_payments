<?php
/**
 * File Process.php
 *
 * @version GIT $Id$
 */
/**
 * Class Process
 */
class Mage_CashCloud_Block_Process extends Mage_Core_Block_Template
{
    public function __construct(array $args = array())
    {
        $this->setTemplate('cashcloud/process.phtml');
        parent::__construct($args);
    }

}
