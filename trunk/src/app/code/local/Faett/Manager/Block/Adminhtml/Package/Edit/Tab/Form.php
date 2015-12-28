<?php

/**
 * Faett_Manager_Block_Adminhtml_Package_Edit_Tab_Form
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
class Faett_Manager_Block_Adminhtml_Package_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * Prepare the form itself.
     *
     * @return Faett_Manager_Block_Adminhtml_Package_Edit_Tab_Form
     * 		The prepared form
     */
    protected function _prepareForm()
    {
        // initialize the form
        $form = new Varien_Data_Form();

        $form->setFieldsetElementRenderer(
            new Faett_Manager_Block_Adminhtml_Package_Edit_Form_Renderer_Fieldset_Element()
        );

        $this->setForm($form);
        // add a fieldset
        $fieldset = $form->addFieldset(
      	    'package_form',
            array(
          	    'legend'=>Mage::helper('manager')->__('104.legend.package-information')
            )
        );
        // add the package name
        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('manager')->__('104.label.name'),
            'class'     => 'required-entry',
            'required'  => false,
            'name'      => 'name',
        ));
        // add the package summary
        $fieldset->addField('summary', 'editor', array(
            'name'      => 'summary',
            'label'     => Mage::helper('manager')->__('104.label.summary'),
            'title'     => Mage::helper('manager')->__('104.title.summary'),
            'style'     => 'width:700px; height:500px;',
            'wysiwyg'   => true,
            'required'  => false,
        ));
        // set the data if available
        if (Mage::getSingleton('adminhtml/session')->getData('package_data')) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getData('package_data'));
            Mage::getSingleton('adminhtml/session')->setData('package_data', null);
        } elseif (Mage::registry('package_data')) {
            $form->setValues(Mage::registry('package_data')->getData());
        }
        // call the parent class
        return parent::_prepareForm();
    }
}