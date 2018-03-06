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
            $items = $quote->getAllItems();

            $itemsSuppliers = array();

            /** @var $item Mage_Sales_Model_Quote_Item */
            foreach ($items as $item) {
                if ($item->getParentItemId()) {
                    continue;
                }
                $itemsSuppliers[(int)$item->getId()] = $this->_getSupplierIds($item->getProductId());
            }
            ksort($itemsSuppliers);
            $itemsSuppliers = $this->_chooseBestSupplier($itemsSuppliers);

            foreach ($items as $item) {
                if ($item->getParentItemId()) {
                    $id = $item->getParentItemId();
                } else {
                    $id = $item->getId();
                }
                $item->setSupplierId($itemsSuppliers[$id]);
            }
            $this->_addedSupplierIdToQuote = true;
        }
        return $this;
    }

    protected function _getSupplierIds($productId)
    {
        $suppliersProducts = Mage::getResourceModel('tdk_dropship/supplierProduct_collection')
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToSelect('supplier_id');

        $supplierIds = array();
        foreach ($suppliersProducts as $sp) {
            $supplierIds[] = (int)$sp->getSupplierId();
        }

        sort($supplierIds);
        return $supplierIds;

    }

    protected function _chooseBestSupplier(array $itemsSuppliers)
    {
        $cacheKey = $this->_getCacheKey($itemsSuppliers);
        if (false !== ($data = Mage::app()->getCache()->load($cacheKey))) {
            return unserialize($data);
        }


        $result = array();
        foreach ($itemsSuppliers as $itemId => $__suppliers) {
            if (empty($__suppliers)) {
                $result[$itemId] = -1;
                unset($itemsSuppliers[$itemId]);
            }
        }
        if (count($itemsSuppliers) > 0) {
            $caseSuppliers = $this->_allCaseSuppliers($itemsSuppliers);

            foreach ($caseSuppliers as $suppliers) {
                if (!isset($min)) {
                    $min = count($suppliers);
                    continue;
                }
                if (count($suppliers) < $min) {
                    $min = count($suppliers);
                }
            }

            $bestCases = array();

            foreach ($caseSuppliers as $suppliers) {
                if (count($suppliers) === $min) {
                    $bestCases[] = $suppliers;
                }
            }

            foreach ($bestCases as $suppliers) {
                if (!isset($bestCase)) {
                    $bestCase = $suppliers;
                    $bestCaseDistance = $this->_getTotalDistance($suppliers);
                    continue;
                }
                $distance = $this->_getTotalDistance($suppliers);
                if ($distance < $bestCaseDistance) {
                    $bestCase = $suppliers;
                    $bestCaseDistance = $distance;
                }
            }

            foreach ($itemsSuppliers as $itemId => $__suppliers) {
                $result[$itemId] = $this->_getBestSupplier($__suppliers);
                unset($itemsSuppliers[$itemId]);
            }
        }
        Mage::app()->getCache()->save(serialize($bestCase), $cacheKey);

        return $result;

    }

    protected function _allCaseSuppliers(array $itemsSuppliers)
    {
        if (count($itemsSuppliers) === 1) {
            $suppliers = array_pop($itemsSuppliers);
            return array_map(function ($item) {
                return array($item);
            }, $suppliers);
        }

        $suppliers = array_pop($itemsSuppliers);

        $allCaseBefore = $this->_allCaseSuppliers($itemsSuppliers);

        $newCase = array();
        foreach ($allCaseBefore as $_suppliersBefore) {
            $count = 0;
            foreach ($suppliers as $supplier) {
                if (!in_array($supplier, $_suppliersBefore)) {
                    $newArray = $_suppliersBefore;
                    $newArray[] = $supplier;
                    $newCase[] = $newArray;
                    $count++;
                }
            }
            if ($count === 0) {
                $newCase[] = $_suppliersBefore;
            }
        }

        return $newCase;

    }

    protected function _getTotalDistance($suppliers)
    {

        $distance = 0;
        foreach ($suppliers as $supplierId) {
            $distance += $this->_getDistanceSupplier($supplierId);
        }

        return $distance;
    }

    protected $_distances = array();

    protected function _getDistanceSupplier($supplierId)
    {
        if (!isset($this->_distances[$supplierId])) {
            $quote = Mage::getSingleton('checkout/cart')->getQuote();
            $customerZipcode = $quote->getShippingAddress()->getPostcode();
            $customerCountry = $quote->getShippingAddress()->getCountryId();

            $supplier = Mage::helper('tdk_dropship/supplierProxy')->getSupplier($supplierId);

            $this->_distances[$supplierId] = Mage::helper('tdk_zipcode')->getDistance($customerZipcode, $customerCountry, $supplier->getPostcode(), $supplier->getCountryId());
        }
        return $this->_distances[$supplierId];
    }

    protected function _getCacheKey($itemsSuppliers)
    {
        $quote = Mage::getSingleton('checkout/cart')->getQuote();
        $customerZipcode = $quote->getShippingAddress()->getPostcode();
        $customerCountry = $quote->getShippingAddress()->getCountryId();

        return md5(serialize($itemsSuppliers) . $customerCountry . $customerZipcode);
    }

    protected function _getBestSupplier($suppliers)
    {
        if (count($suppliers) === 1) {
            return reset($suppliers);
        }
        foreach ($suppliers as $supplierId) {
            if (!isset($minId)) {
                $minId = $supplierId;
                $minDistance = $this->_getDistanceSupplier($supplierId);
            }
            $distance = $this->_getDistanceSupplier($supplierId);
            if ($distance < $minDistance) {
                $minId = $supplierId;
                $minDistance = $distance;

            }
        }
        return $minId;
    }
}