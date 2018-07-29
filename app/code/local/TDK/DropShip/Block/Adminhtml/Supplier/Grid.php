<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/17/18
 * Time: 12:36 PM
 */
class TDK_DropShip_Block_Adminhtml_Supplier_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('suppliersGrid');
        $this->setDefaultSort('supplier_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tdk_dropship/supplier')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('supplier_id',
            array(
                'header' => 'ID',
                'align' => 'right',
                'width' => '50px',
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


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}