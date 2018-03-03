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
    public function addSupplierIdToQuote(Mage_Sales_Model_Quote_Address $customerAddress)
    {
        // fake random suppliers
        $quote = $customerAddress->getQuote();
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            $item->setSupplierId(rand(1, 3));
        }
    }
}