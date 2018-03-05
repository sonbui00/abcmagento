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

$installer->getConnection()
    ->addColumn(
        $this->getTable('sales/shipment_track'),
        'status',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'unsigned'  => true,
            'nullable'  => true,
            'comment'   => 'Status of shipment, 10 is error, 20 is delivered',
        )
    );

$installer->endSetup();