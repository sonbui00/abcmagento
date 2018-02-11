<?php
class SM_XPos_Model_Discount_Creditmemo_Totals extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $a = $order->getDiscountAmount();

        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $a);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $a);

//        $creditmemo->setMwRewardpoint($order->getMwRewardpoint());
        $creditmemo->setXposDiscountShow($a);
        Mage::getModel('adminhtml/session')->setData('xpos_creditmemo_discount', $a);
//        $creditmemo->setMwRewardpointDiscount($baseTotalDiscountAmount);

        return $this;
    }
}
