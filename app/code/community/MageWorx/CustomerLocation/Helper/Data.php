<?php
/**
 * MageWorx
 * Customer Location Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerLocation
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerLocation_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_ENABLED_IN_ORDERS = 'mageworx_geoip/mageworx_customerlocation/enable_orders';
    const XML_ENABLED_IN_ONLINE_CUSTOMERS = 'mageworx_geoip/mageworx_customerlocation/enable_online_customers';

    /**
     * Checks if extension enabled for order view
     *
     * @return bool
     */
    public function isEnabledForOrders()
    {
        return Mage::getStoreConfigFlag(self::XML_ENABLED_IN_ORDERS);
    }

    /**
     * Checks if extension enabled for "online customers" grid
     *
     * @return bool
     */
    public function isEnabledForCustomers()
    {
        return Mage::getStoreConfigFlag(self::XML_ENABLED_IN_ONLINE_CUSTOMERS);
    }

    /**
     * Returns location html
     *
     * @param mixed $obj
     * @return mixed
     */
    public function getGeoIpHtml($obj)
    {
        $block = Mage::app()->getLayout()
            ->createBlock('core/template')
            ->setTemplate('mageworx/customerlocation/adminhtml-customer-geoip.phtml')
            ->addData(array('item' => $obj))
            ->toHtml();

        return $block;
    }
}