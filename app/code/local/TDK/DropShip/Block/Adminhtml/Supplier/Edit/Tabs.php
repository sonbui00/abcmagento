<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/17/18
 * Time: 12:56 PM
 */
class TDK_DropShip_Block_Adminhtml_Supplier_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('suppliers_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Supplier Information');
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label' => 'Supplier Information',
            'title' => 'Supplier Information',
            'content' => $this->getLayout()
                ->createBlock('tdk_dropship/adminhtml_supplier_edit_tab_form')
                ->toHtml()
        ));

        return parent::_beforeToHtml();
    }
}