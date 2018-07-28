<?php
/**
 * MageWorx
 * Customer Location Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerLocation
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

$installer = $this;

$installer->startSetup();

$pathLike = 'mageworx_customers/geolocation/%';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->getSelect()->where('path like ?', $pathLike);

foreach ($configCollection as $conf) {
    $path = $conf->getPath();
    $path = str_replace('mageworx_customers/geolocation', 'mageworx_geoip/customerlocation', $path);
    $conf->setPath($path)->save();
}