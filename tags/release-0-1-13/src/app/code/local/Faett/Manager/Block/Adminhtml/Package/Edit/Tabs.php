<?php

/**
 * Faett_Manager_Block_Adminhtml_Package_Edit_Tabs
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
class Faett_Manager_Block_Adminhtml_Package_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs {

    /**
     * Initialize the block.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('package_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('manager')->__('105.title.package-information'));
    }

    /**
     * Invoked before the toHtml() method is invoked
     * and adds a new tab.
     *
     * @return Faett_Manager_Block_Adminhtml_Package_Edit_Tabs
     * 		The instance itself
     */
    protected function _beforeToHtml()
    {
        // add a new tab with the package information
        $this->addTab('form_section', array(
            'label'     => Mage::helper('manager')->__('105.label.package-information'),
            'title'     => Mage::helper('manager')->__('105.title.package-information'),
            'content'   => $this->getLayout()->createBlock('manager/adminhtml_package_edit_tab_form')->toHtml(),
        ));
        // return the tabs
        return parent::_beforeToHtml();
    }
}