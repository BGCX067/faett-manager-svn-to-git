<?php

/**
 * Faett_Manager_Block_Adminhtml_Channel_Edit_Tab_Form
 *
 * NOTICE OF LICENSE
 * 
 * Faett_Manager is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Faett_Manager is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Faett_Manager.  If not, see <http://www.gnu.org/licenses/>.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Faett_Manager to newer
 * versions in the future. If you wish to customize Faett_Manager for your
 * needs please refer to http://www.faett.net for more information.
 *
 * @category   Faett
 * @package    Faett_Manager
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 */

/**
 * @category   	Faett
 * @package    	Faett_Manager
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Manager_Block_Adminhtml_Channel_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $this->setForm($form);

        $fieldset = $form->addFieldset('channel_form', array(
      	  'legend' => Mage::helper('manager')->__('100.legend.channel-information'))
        );

        $fieldset->addField('url', 'text', array(
            'label'     => Mage::helper('manager')->__('100.label.url'),
            'required'  => true,
            'name'      => 'url',
        ));

        $fieldset->addField('alias', 'text', array(
            'label'     => Mage::helper('manager')->__('100.label.alias'),
            'required'  => true,
            'name'      => 'alias',
	    ));

	    $fieldset->addField('username', 'text', array(
            'label'     => Mage::helper('manager')->__('100.label.username'),
            'class'     => 'validate-email',
            'required'  => false,
            'name'      => 'username',
	    ));

	    $fieldset->addField('new_password', 'password', array(
            'label'     => Mage::helper('manager')->__('100.label.password'),
            'class'     => 'validate-password',
            'required'  => false,
            'name'      => 'new_password',
	    ));

	    $fieldset->addField('confirmation', 'password', array(
            'label'     => Mage::helper('manager')->__('100.label.confirmation'),
            'class'     => 'validate-cpassword',
            'required'  => false,
            'name'      => 'confirmation',
	    ));

        if (Mage::getSingleton('adminhtml/session')->getData('channel_data')) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getData('channel_data'));
            Mage::getSingleton('adminhtml/session')->setData('channel_data', null);
        } elseif (Mage::registry('channel_data') ) {
            $form->setValues(Mage::registry('channel_data')->getData());
        }

        return parent::_prepareForm();
    }
}