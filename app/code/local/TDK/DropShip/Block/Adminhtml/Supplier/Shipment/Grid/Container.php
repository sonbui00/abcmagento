<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/17/18
 * Time: 12:36 PM
 */
class TDK_DropShip_Block_Adminhtml_Supplier_Shipment_Grid_Container
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        //indicate where we can find the controller
        $this->_controller = 'adminhtml_supplier_shipment';
        $this->_blockGroup = 'tdk_dropship';
        //header text
        $this->_headerText = 'Shipments';
        //button label

        Mage_Adminhtml_Block_Widget_Container::__construct();

        $this->setTemplate('widget/grid/container.phtml');
    }
}