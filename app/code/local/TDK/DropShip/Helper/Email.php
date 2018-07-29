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
    const XML_PATH_EMAIL_TEMPLATE_SUPPLIER_DELIVERED_SHIPMENT = 'shipping/dropship/supplier_delivered_shipment_email_template';
    const XML_PATH_EMAIL_TEMPLATE_CUSTOMER_DELIVERED_SHIPMENT = 'shipping/dropship/customer_delivered_shipment_email_template';

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

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @param $supplier
     * @return $this
     */
    public function deliveredShipmentSupplier($shipment, $supplier)
    {
        if (is_int($supplier)) {
            $supplier = Mage::helper('tdk_dropship/supplierProxy')->getSupplier($supplier);
        }

        $order = $shipment->getOrder();
        $storeId = $order->getStore()->getId();

        if (!$this->canSendDeliveredShipmentSupplier($storeId)) {
            return false;
        }

        // Retrieve corresponding email template id and customer name
        $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_SUPPLIER_DELIVERED_SHIPMENT, $storeId);

        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');

        $emailInfo->addTo($supplier->getEmail(), $supplier->getFullName());

        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'shipment'     => $shipment,
            )
        );
        $mailer->send();

        return $this;

    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return $this
     * @throws Exception
     */
    public function deliveredShipmentCustomer($shipment)
    {
        $order = $shipment->getOrder();
        $storeId = $order->getStore()->getId();

        if (!$this->canSendDeliveredShipmentCustomer($storeId)) {
            return false;
        }

        // Retrieve corresponding email template id and customer name
        $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE_CUSTOMER_DELIVERED_SHIPMENT, $storeId);

        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');

        $emailInfo->addTo($order->getCustomerEmail(), $customerName);

        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(Mage_Sales_Model_Order::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $order,
                'shipment'     => $shipment,
            )
        );
        $mailer->send();

        return $this;

    }

    protected function canSendNewOrderSupplierEmail($storeId)
    {
        return true;
    }

    protected function canSendDeliveredShipmentSupplier($storeId)
    {
        return true;
    }

    protected function canSendDeliveredShipmentCustomer($storeId)
    {
        return true;
    }
}