<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/17/18
 * Time: 12:58 PM
 */
class TDK_DropShip_Block_Adminhtml_Supplier_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('suppliers_form',
            array('legend' => 'Supplier Information'));
        $fieldset->addField('first_name', 'text',
            array(
                'label' => 'First Name',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'first_name',
            ));

        $fieldset->addField('last_name', 'text',
            array(
                'label' => 'Last Name',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'last_name',
            ));

        $fieldset->addField('email', 'text',
            array(
                'label' => 'Email',
                'class' => 'required-entry validate-email',
                'required' => true,
                'name' => 'email',
            ));

        $fieldset->addField('street_address', 'multiline',
            array(
                'label' => 'Street Address',
                'class' => 'required-entry',
                'required' => true,
                'name' => 'street_address',
            ));

        $fieldset->addField('city', 'text',
            array(
                'label' => 'City',
                'class' => '',
                'required' => false,
                'name' => 'city',
            ));

        $fieldset->addField('country_id', 'select',
            array(
                'label' => 'Country',
                'class' => 'required-entry countries',
                'required' => true,
                'name' => 'country_id',
                'values' => $this->countryOptionArray(),
            ));

        $countryID = null;
        if (Mage::registry('current_supplier')) {
            $countryID = Mage::registry('current_supplier')->getCountryId();
        }

        $fieldset->addField('region_id', 'select',
            array(
                'label' => 'State/Province',
                'class' => 'required-entry',
                'required' => false,
                'name' => 'region_id',
                'values' => $this->countryRegionOptionArray($countryID)
            ));

//        $fieldset->addField('region_id', 'hidden',
//            array(
//                'label' => 'State/Province',
//                'class' => '',
//                'required' => false,
//                'name' => 'region_id',
//            ));

        $fieldset->addField('postcode', 'text',
            array(
                'name' => 'postcode',
                'label' => 'Zip/Postal Code',
                'class' => ' required-entry',
                'required' => '1',
                'note' => NULL,
            ));
        $fieldset->addField('telephone', 'text',
            array(
                'name' => 'telephone',
                'label' => 'Telephone',
                'class' => ' required-entry',
                'required' => '1',
                'note' => NULL,
            ));

        $fieldsetLogin = $form->addFieldset('suppliers_form_password',
            array('legend' => 'Login Management'));
//
//        $fieldsetLogin->addField('new_password', 'text',
//            array(
//                'label' => 'New Password',
//                'class' => 'validate-new-password',
//                'required' => false,
//                'name' => 'new_password',
//            ));
//
//        $fieldsetLogin->addField('new_password', 'text',
//            array(
//                'label' => 'New Password',
//                'class' => 'validate-new-password',
//                'required' => false,
//                'name' => 'new_password',
//            ));

        $fieldsetLogin->addField('username', 'text', array(
                'name'  => 'username',
                'label' => Mage::helper('adminhtml')->__('User Name'),
                'title' => Mage::helper('adminhtml')->__('User Name'),
                'required' => true,
            )
        );

        $fieldsetLogin->addField('password', 'password', array(
                'name'  => 'new_password',
                'label' => Mage::helper('adminhtml')->__('New Password'),
                'title' => Mage::helper('adminhtml')->__('New Password'),
                'class' => 'input-text validate-admin-password',
            )
        );

        $fieldsetLogin->addField('confirmation', 'password', array(
                'name'  => 'password_confirmation',
                'label' => Mage::helper('adminhtml')->__('Password Confirmation'),
                'class' => 'input-text validate-cpassword',
            )
        );

//        $fieldsetLogin->addField('current_password', 'obscure',
//            array(
//                'label' => 'Current Password',
//                'class' => '',
//                'required' => true,
//                'name' => 'current_password',
//            ));


        if (Mage::registry('current_supplier')) {
            $form->setValues(Mage::registry('current_supplier')->getData());
        }

        return parent::_prepareForm();
    }

    public function countryOptionArray()
    {
        return Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
    }

    public function countryRegionOptionArray($countryId)
    {
        if (!isset($countryId)) {
            return array();
        }

        return Mage::getResourceModel('directory/region_collection')
            ->addCountryFilter($countryId)
            ->load()
            ->toOptionArray();
    }

}