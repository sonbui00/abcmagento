<?php
/**
 * MageWorx
 * Customer Location Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerLocation
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CustomerLocation_Model_Observer
{
    /**
     * Adds GeoIP location html to order view
     *
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerLocation_Model_Observer
     */
    public function orderLocation(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('mageworx_customerlocation')->isEnabledForOrders()) {
            return $this;
        }

        $_order = null;
        $block = $observer->getEvent()->getBlock();
        $controller = Mage::app()->getRequest()->getControllerName();
        if($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Info && $controller == 'sales_order'){
            $_order = $block->getOrder();
        } elseif($block instanceof Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form && $controller == 'sales_order_shipment'){
            $_order = $block->getShipment()->getOrder();
        } elseif($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Info && $controller == 'sales_order_shipment'){
            $_order = $block->getOrder();
        } elseif($block instanceof Mage_Adminhtml_Block_Sales_Order_Invoice_View_Form && $controller == 'sales_order_invoice'){
            $_order = $block->getInvoice()->getOrder();
        } elseif($block instanceof Mage_Adminhtml_Block_Sales_Order_View_Info && $controller == 'sales_order_invoice'){
            $_order = $block->getOrder();
        }

        if(!is_null($_order)) {
            $ip = $_order->getRemoteIp();
            if (!$ip) {
                return $this;
            }

            $geoIpObj = Mage::getModel('mageworx_geoip/geoip')->getLocation($ip);

            if ($geoIpObj->getCode()) {
                $transport = $observer->getTransport();
                $html = $transport->getHtml();

                $obj = new Varien_Object();
                $obj->addData(array(
                    'geo_ip' => $geoIpObj,
                    'ip' => $ip,
                ));
                $geoIpHtml = Mage::helper('mageworx_customerlocation')->getGeoIpHtml($obj);
                $html = str_ireplace($ip, $geoIpHtml, $html);

                $transport->setHtml($html);
            }
        }

        return $this;
    }

    /**
     * Adds GeoIP location to new column in "online customers grid"
     *
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerLocation_Model_Observer
     */
    public function onlineCustomerLocation(Varien_Event_Observer $observer)
    {
        if (!($block = $observer->getEvent()->getBlock()) || !($block instanceof Mage_Adminhtml_Block_Customer_Online_Grid)) {
            return $this;
        }

        if (!Mage::helper('mageworx_customerlocation')->isEnabledForCustomers()) {
            return $this;
        }

        $block->addColumnAfter('geoip', array(
            'header'    => Mage::helper('mageworx_geoip')->__('IP Location'),
            'index'     => 'remote_addr',
            'align'     => 'left',
            'width'     => 200,
            'renderer'  => 'mageworx_customerlocation/adminhtml_customer_online_grid_renderer_geoip',
            'filter'    => false,
            'sortable'  => false,
        ), 'ip_address');

        return $this;
    }
}