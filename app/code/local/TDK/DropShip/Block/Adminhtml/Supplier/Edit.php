<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/17/18
 * Time: 12:47 PM
 */
class TDK_DropShip_Block_Adminhtml_Supplier_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        //you will notice that assigns the same blockGroup the Grid Container
        $this->_blockGroup = 'tdk_dropship';
        // and the same container
        $this->_controller = 'adminhtml_supplier';
        //we define the labels for the buttons save and delete
        $this->_updateButton('save', 'label', 'Save');
        $this->_updateButton('delete', 'label', 'Delete');
    }

    /* Here, we look at whether it was transmitted item to form
     * to put the right text in the header (Add or Edit)
     */

    public function getHeaderText()
    {
        if (Mage::registry('current_supplier') && Mage::registry('current_supplier')->getId()) {
            return 'Edit supplier \'' . $this->escapeHtml(Mage::registry('current_supplier')->getFullName()) . '\'';
        } else {
            return 'Add a new supplier';
        }
    }
}