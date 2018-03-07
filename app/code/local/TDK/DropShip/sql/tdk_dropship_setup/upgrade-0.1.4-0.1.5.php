<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer->startSetup();

/* email supplier when tracking is delivered */

$emailContent = <<<HTML
{{template config_path="design/email/header"}}
{{inlinecss file="email-inline.css"}}

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="email-heading">
                        <h1>Thank you for your support {{var store.getFrontendName()}} to ship items.</h1>
                        <p>Items from shipment #{{var shipment.increment_id}} was delivery success.</p>
                    </td>
                    <td class="store-info">
                        <h4>Order Questions?</h4>
                        <p>
                            {{depend store_phone}}
                            <b>Call Us:</b>
                            <a href="tel:{{var phone}}">{{var store_phone}}</a><br>
                            {{/depend}}
                            {{depend store_hours}}
                            <span class="no-link">{{var store_hours}}</span><br>
                            {{/depend}}
                            {{depend store_email}}
                            <b>Email:</b> <a href="mailto:{{var store_email}}">{{var store_email}}</a>
                            {{/depend}}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="order-details">
            <h3>Shipment <span class="no-link">#{{var shipment.increment_id}}</span></h3>
            <p>Order <span class="no-link">#{{var order.increment_id}}</span></p>
        </td>
    </tr>
    <tr class="order-information">
        <td>
            {{layout handle="sales_email_order_shipment_items" shipment=\$shipment order=\$order}}
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    {{depend order.getIsNotVirtual()}}
                    <td class="address-details">
                        <h6>Ship to:</h6>
                        <p><span class="no-link">{{var order.shipping_address.format('html')}}</span></p>
                    </td>
                    <td class="method-info">
                        <h6>Shipping method:</h6>
                        <p>{{var order.shipping_description}}</p>
                    </td>
                    {{/depend}}
                </tr>
            </table>
            {{block type='core/template' area='frontend' template='email/order/shipment/track.phtml' shipment=\$shipment order=\$order}}
        </td>
    </tr>
</table>

{{template config_path="design/email/footer"}}
HTML;

$data = array(
    'template_code' => "Supplier Delivered Shipment",
    'template_text' => $emailContent ,
    'template_styles' => NULL,
    'template_type' => Mage_Newsletter_Model_Template::TYPE_HTML,
    'template_subject' => '{{var store.getFrontendName()}}: Delivered Shipment # {{var shipment.increment_id}} for Order # {{var order.increment_id}}',
    'template_sender_name' => Mage::getStoreConfig('trans_email/ident_general/name'),
    'template_sender_email' => Mage::getStoreConfig('trans_email/ident_general/email'),
    'template_actual' => 1,
    'added_at' => Mage::getSingleton('core/date')->gmtDate(),
    'modified_at' => Mage::getSingleton('core/date')->gmtDate()
);

$model = Mage::getModel('core/email_template')->setData($data);

try {
    $model->save();
    $config = Mage::getModel('core/config_data')
        ->load(TDK_DropShip_Helper_Email::XML_PATH_EMAIL_TEMPLATE_SUPPLIER_DELIVERED_SHIPMENT, 'path');

    if (!$config->getId()) {
        $config->setValue($model->getId())
            ->setPath(TDK_DropShip_Helper_Email::XML_PATH_EMAIL_TEMPLATE_SUPPLIER_DELIVERED_SHIPMENT)
            ->setScopeId(0)
            ->setScope('default')
            ->save();
    }
} catch (Exception $e){
    Mage::logException($e->getMessage());
}

/* email customer when tracking is delivered */

$emailContent = <<<HTML
{{template config_path="design/email/header"}}
{{inlinecss file="email-inline.css"}}

<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td class="email-heading">
                         <h1>Thank you for your order from {{var store.getFrontendName()}}.</h1>
                        <p>Items from shipment #{{var shipment.increment_id}} was delivery success.</p>
                    </td>
                    <td class="store-info">
                        <h4>Order Questions?</h4>
                        <p>
                            {{depend store_phone}}
                            <b>Call Us:</b>
                            <a href="tel:{{var phone}}">{{var store_phone}}</a><br>
                            {{/depend}}
                            {{depend store_hours}}
                            <span class="no-link">{{var store_hours}}</span><br>
                            {{/depend}}
                            {{depend store_email}}
                            <b>Email:</b> <a href="mailto:{{var store_email}}">{{var store_email}}</a>
                            {{/depend}}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="order-details">
            <h3>Shipment <span class="no-link">#{{var shipment.increment_id}}</span></h3>
            <p>Order <span class="no-link">#{{var order.increment_id}}</span></p>
        </td>
    </tr>
    <tr class="order-information">
        <td>
            {{layout handle="sales_email_order_shipment_items" shipment=\$shipment order=\$order}}
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    {{depend order.getIsNotVirtual()}}
                    <td class="address-details">
                        <h6>Ship to:</h6>
                        <p><span class="no-link">{{var order.shipping_address.format('html')}}</span></p>
                    </td>
                    <td class="method-info">
                        <h6>Shipping method:</h6>
                        <p>{{var order.shipping_description}}</p>
                    </td>
                    {{/depend}}
                </tr>
            </table>
            {{block type='core/template' area='frontend' template='email/order/shipment/track.phtml' shipment=\$shipment order=\$order}}
        </td>
    </tr>
</table>

{{template config_path="design/email/footer"}}
HTML;

$data = array(
    'template_code' => "Customer Delivered Shipment",
    'template_text' => $emailContent ,
    'template_styles' => NULL,
    'template_type' => Mage_Newsletter_Model_Template::TYPE_HTML,
    'template_subject' => '{{var store.getFrontendName()}}: Delivered Shipment # {{var shipment.increment_id}} for Order # {{var order.increment_id}}',
    'template_sender_name' => Mage::getStoreConfig('trans_email/ident_general/name'),
    'template_sender_email' => Mage::getStoreConfig('trans_email/ident_general/email'),
    'template_actual' => 1,
    'added_at' => Mage::getSingleton('core/date')->gmtDate(),
    'modified_at' => Mage::getSingleton('core/date')->gmtDate()
);

$model = Mage::getModel('core/email_template')->setData($data);

try {
    $model->save();
    $config = Mage::getModel('core/config_data')
        ->load(TDK_DropShip_Helper_Email::XML_PATH_EMAIL_TEMPLATE_CUSTOMER_DELIVERED_SHIPMENT, 'path');

    if (!$config->getId()) {
        $config->setValue($model->getId())
            ->setPath(TDK_DropShip_Helper_Email::XML_PATH_EMAIL_TEMPLATE_CUSTOMER_DELIVERED_SHIPMENT)
            ->setScopeId(0)
            ->setScope('default')
            ->save();
    }
} catch (Exception $e){
    Mage::logException($e->getMessage());
}



$installer->endSetup();