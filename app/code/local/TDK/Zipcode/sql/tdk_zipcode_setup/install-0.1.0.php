<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/5/18
 * Time: 11:00 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$installer->run(
    'DROP TABLE IF EXISTS ' . $installer->getTable('tdk_zipcode/coordinate') . ';'
);
/*----------------------------------*/

$table = $installer->getConnection()
    ->newTable($installer->getTable('tdk_zipcode/coordinate'))
    ->addColumn('coordinate_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('zipcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable' => false,
    ), 'Zipcode or country code')
    ->addColumn('lat', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'nullable' => false,
        'unsigned' => false,
    ), 'lat')
    ->addColumn('lng', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'nullable' => false,
        'unsigned' => false,
    ), 'lng')
    ->addIndex(
        $installer->getIdxName(
            'tdk_zipcode/coordinate',
            'zipcode',
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        'zipcode', array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));

$installer->getConnection()->createTable($table);

$installer->endSetup();