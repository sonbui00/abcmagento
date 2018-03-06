<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/3/18
 * Time: 10:09 PM
 */

class TDK_DropShip_Block_Sales_Order_View_Tab_DropShipping
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_drop_shipping');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tdk_dropship/supplier_collection')
            ->join(
                array('supplierOrder' => 'tdk_dropship/supplier_order'),
                'main_table.supplier_id = supplierOrder.supplier_id',
                array('order_id', 'shipment_id', 'shipment_increment_id')
            )
            ->addFieldToFilter('order_id', $this->getOrder()->getId())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('supplier_id',
            array(
                'header' => 'ID',
                'align' => 'right',
                'index' => 'supplier_id',
            ));

        $this->addColumn('first_name',
            array(
                'header' => 'First Name',
                'align' => 'left',
                'index' => 'first_name',
            ));
        $this->addColumn('last_name',
            array(
                'header' => 'Last Name',
                'align' => 'left',
                'index' => 'last_name',
            ));

        $this->addColumn('email',
            array(
                'header' => 'Email',
                'align' => 'left',
                'index' => 'email',
            ));

        $this->addColumn('shipment',
            array(
                'header' => 'Shipment',
                'align' => 'left',
                'index' => 'shipment_increment_id',
            ));

        return parent::_prepareColumns();
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getRowUrl($row)
    {
        if ($row->getShipmentId()) {
            return $this->getUrl(
                '*/sales_order_shipment/view',
                array(
                    'shipment_id'=> $row->getShipmentId(),
                    'order_id'  => $this->getOrder()->getId(),
                ));
        } else {
            return $this->getUrl(
                '*/sales_order_shipment/new',
                array(
                    'supplier_id'=> $row->getId(),
                    'order_id'  => $this->getOrder()->getId(),
                ));
        }
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/supplier/orderGrid', array('_current' => true));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('sales')->__('Drop Shipping');
    }

    public function getTabTitle()
    {
        return Mage::helper('sales')->__('Drop Shipping');
    }

    public function canShowTab()
    {
        if ($this->getOrder()->getIsVirtual()) {
            return false;
        }
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
