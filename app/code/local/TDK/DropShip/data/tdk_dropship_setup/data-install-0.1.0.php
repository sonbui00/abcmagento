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

/* @TODO: Remove after dev */

$suppliers = array(
    array(
        'id' => 1,
        'first_name' => 'Kenneth',
        'last_name' => 'Fox',
        'email' => 'fox@yopmail.com',
        'street_address' => array('2104 Scheuvront Drive', ''),
        'city' => 'City',
        'country_id' => 'US',
        'region_id' => '13',
        'postcode' => '80303',
        'telephone' => '303-497-3094',
        'username' => 'Kenneth',
        'new_password' => 'admin123',
    ),
    array(
        'id' => 2,
        'first_name' => 'Cheryl',
        'last_name' => 'Larsen',
        'email' => 'cheryl@yopmail.com',
        'street_address' => array('4548 Driftwood Road', ''),
        'city' => 'City',
        'country_id' => 'US',
        'region_id' => '12',
        'postcode' => '94107',
        'telephone' => '408-340-1003',
        'username' => 'Cheryl',
        'new_password' => 'admin123',
    ),
    array(
        'id' => 3,
        'first_name' => 'Judith',
        'last_name' => 'Wesley',
        'email' => 'judith@yopmail.com',
        'street_address' => array('1537 Cameron Road', ''),
        'city' => 'City',
        'country_id' => 'US',
        'region_id' => '43',
        'postcode' => '14204',
        'telephone' => '716-341-9870',
        'username' => 'Judith',
        'new_password' => 'admin123',
    )
);
$suppliersModel = array();

foreach($suppliers as $supplier) {
    $suppliersModel[] = Mage::getModel('tdk_dropship/supplier')->setData($supplier)->save();
}

$productIds = Mage::getResourceModel('catalog/product_collection')->getAllIds();

foreach ($productIds as $productId) {
    $suppliersModel[($productId % 3)]->addProductById((int) $productId);
}

$installer->endSetup();