<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/17/18
 * Time: 12:36 PM
 */
class TDK_DropShip_Block_Adminhtml_Supplier_Grid_Container
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        //indicate where we can find the controller
        $this->_controller = 'adminhtml_supplier';
        $this->_blockGroup = 'tdk_dropship';
        //header text
        $this->_headerText = 'Manage Suppliers';
        //button label
        $this->_addButtonLabel = 'Add a supplier';
        parent::__construct();
    }
}