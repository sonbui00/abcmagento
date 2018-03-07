<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/7/18
 * Time: 4:31 PM
 */
class TDK_DropShip_Helper_Email extends Mage_Core_Helper_Abstract
{

    const XML_PATH_EMAIL_TEMPLATE_NEW_ORDER_SUPPLIER = 'shipping/dropship/supplier_new_order_email_template';
    /**
     * @param Mage_Sales_Model_Order $order
     * @return $order
     * @throws Exception
     */
    public function newOrderSupplier($order, $supplier)
    {
        if (is_int($supplier)) {
            $supplier = Mage::helper('tdk_dropship/supplierProxy')->getSupplier($supplier);
        }
        $storeId = $order->getStore()->getId();

        if (!$this->canSendNewOrderSupplierEmail($storeId)) {
            return false;
        }

        // Retrieve corresponding email template id and customer name
        $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_NEW_ORDER_SUPPLIER, $storeId);
        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        /** @var $emailInfo Mage_Core_Model_Email_Info */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($supplier->getEmail(), $supplier->getFullName());

        $mailer->addEmailInfo($emailInfo);


        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
            'order'        => $order,
            'supplier'     => $supplier,
        ));

        /** @var $emailQueue Mage_Core_Model_Email_Queue */
        $emailQueue = Mage::getModel('core/email_queue');
        $emailQueue->setEntityId($order->getId())
            ->setEntityType(Mage_Sales_Model_Order::ENTITY . '_supplier')
            ->setEventType(Mage_Sales_Model_Order::EMAIL_EVENT_NAME_NEW_ORDER . '_supplier')
            ->setIsForceCheck(false);

        $mailer->setQueue($emailQueue)->send();

        return $this;
    }

    protected function canSendNewOrderSupplierEmail($storeId)
    {
        return true;
    }
}