<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/16/18
 * Time: 8:24 PM
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/* */
$installer->run(
    'DROP TABLE IF EXISTS ' . $installer->getTable('tdk_dropship/supplier') . ';' .
    'DROP TABLE IF EXISTS ' . $installer->getTable('tdk_dropship/supplier_product') . ';'
);
/*----------------------------------*/

$table = $installer->getConnection()
    ->newTable($installer->getTable('tdk_dropship/supplier'))
    ->addColumn('supplier_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Supplier Id')
    ->addColumn('first_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable' => false,
    ), 'First name')
    ->addColumn('last_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'nullable' => false,
    ), 'Last name')
    ->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'Email')
    ->addColumn('street_address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 200, array(), 'Street Address')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(), 'City')
    ->addColumn('country_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(), 'Country ID')
    ->addColumn('region_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 5, array(), 'State/Province')
    ->addColumn('postcode', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(), 'Zip code')
    ->addColumn('telephone', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(), 'telephone')
    ->addColumn('admin_user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => true,
    ), 'Admin User ID')
    ->addIndex(
        $installer->getIdxName(
            'tdk_dropship/supplier',
            'admin_user_id',
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        'admin_user_id', array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));


$installer->getConnection()->createTable($table);

$table = $installer->getConnection()
    ->newTable($installer->getTable('tdk_dropship/supplier_product'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'Id')
    ->addColumn('supplier_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Supplier ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Admin User ID')
    ->addIndex(
        $installer->getIdxName(
            'tdk_dropship/supplier_product',
            array('supplier_id', 'product_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('supplier_id', 'product_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE));


$installer->getConnection()->createTable($table);

$installer->endSetup();