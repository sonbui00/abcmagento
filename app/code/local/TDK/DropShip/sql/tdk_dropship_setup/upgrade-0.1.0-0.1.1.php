<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/2/18
 * Time: 2:46 PM
 */
/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn(
        $this->getTable('sales_flat_quote_item'),
        'supplier_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned'  => true,
            'nullable'  => true,
            'comment'   => 'Supplier ID',
        )
    );
$installer->getConnection()
    ->addColumn(
        $this->getTable('sales_flat_order_item'),
        'supplier_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned'  => true,
            'nullable'  => true,
            'comment'   => 'Supplier ID',
        )
    );


$installer->endSetup();