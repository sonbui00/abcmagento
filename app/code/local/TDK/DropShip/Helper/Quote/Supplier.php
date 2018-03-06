<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/2/18
 * Time: 10:32 PM
 */
class TDK_DropShip_Helper_Quote_Supplier
    extends Mage_Core_Helper_Abstract
{
    protected $_addedSupplierIdToQuote = false;

    public function addSupplierIdToQuote(Mage_Sales_Model_Quote $quote)
    {
        if (Mage::helper('tdk_dropship')->allowDropShipForQuote($quote) && !$this->_addedSupplierIdToQuote) {
            // fake random suppliers
            $items = $quote->getAllItems();
            $i = 0;
            foreach ($items as $item) {
                $item->setSupplierId(($i % 3) + 1);
                $i++;
            }
            $this->_addedSupplierIdToQuote = true;
        }
        return $this;
    }
}