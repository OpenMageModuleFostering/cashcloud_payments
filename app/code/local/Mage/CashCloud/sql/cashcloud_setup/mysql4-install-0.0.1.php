<?php
/**
 * @var Mage_CashCloud_Model_Mysql4_Setup $this
 */
try {
    $installer = $this;
    $quotePaymentTable = $installer->getTable("sales/quote_payment");
    $orderPaymentTable = $installer->getTable("sales/order_payment");

    $installer->startSetup();
    if (!$installer->getConnection()->tableColumnExists($quotePaymentTable, "cashcloud_id")) {
        $installer->getConnection()->addColumn($quotePaymentTable, "cashcloud_id", "varchar(255)");
    }
    if (!$installer->getConnection()->tableColumnExists($orderPaymentTable, "cashcloud_id")) {
        $installer->getConnection()->addColumn($orderPaymentTable, "cashcloud_id", "varchar(255)");
    }
    $installer->endSetup();
} catch (Exception $e) {
    var_dump($e); die();
}
