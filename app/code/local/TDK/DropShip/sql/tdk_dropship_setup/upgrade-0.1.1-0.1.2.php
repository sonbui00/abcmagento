<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 11:19 AM
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* */
$installer->run(
    'DROP TABLE IF EXISTS ' . $installer->getTable('tdk_dropship/supplier_order') . ';'
);
/*----------------------------------*/

$table = $installer->getConnection()
    ->newTable($installer->getTable('tdk_dropship/supplier_order'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('supplier_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => false,
        'nullable' => false,
    ), 'Supplier ID')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Order ID')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'nullable' => true,
    ), 'Qty')
    ->addColumn('shipment_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Shipment ID')
    ->addColumn('shipment_increment_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable' => true,
    ), 'Shipment Increment ID');


$installer->getConnection()->createTable($table);

$installer->endSetup();