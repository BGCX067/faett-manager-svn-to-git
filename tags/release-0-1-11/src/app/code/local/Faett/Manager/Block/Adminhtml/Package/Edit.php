<?php

/**
 * Faett_Manager_Block_Adminhtml_Package_Edit
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
class Faett_Manager_Block_Adminhtml_Package_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Initialize the block.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // initialize the layout
        $this->_objectId = 'id';
        $this->_blockGroup = 'manager';
        $this->_controller = 'adminhtml_package';
        // not display the delete and the reset button
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        // load the package state (update/install)
        $state = Mage::registry('package_data')->getState();
        // add the buttons depending on the package state
        if ($state == Faett_Manager_Helper_Data::PACKAGE_STATE_UPDATE_AVAILABLE) {
            $this->_addButton('install', array(
                'label'     => Mage::helper('manager')->__('106.label.update-package'),
                'onclick'   => 'setLocation(\'' . $this->getUpdateUrl() . '\')',
            ));
        } elseif ($state == Faett_Manager_Helper_Data::PACKAGE_STATE_NOT_INSTALLED) {
            $this->_addButton('install', array(
                'label'     => Mage::helper('manager')->__('106.label.install-package'),
                'onclick'   => 'setLocation(\'' . $this->getInstallUrl() . '\')',
            ));
        }
    }

    /**
     * Returns the URL to the package extension.
     *
     * @param string $params The URL's path
     * @param array $params2 The URL's parameters
     * @return string the requested URL
     */
    public function getUrl($params = '', $params2 = array())
    {
        $params2['id'] = Mage::registry('package_data')->getId();
        return parent::getUrl($params, $params2);
    }

    /**
     * Returns the URL for installing the package.
     *
     * @return the update URL
     */
    public function getInstallUrl()
    {
        return $this->getUrl('*/*/install');
    }

    /**
     * Returns the URL for updating the package.
     *
     * @return the update URL
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('*/*/update');
    }

    /**
     * Returns the header text.
     *
     * @return string The header text
     */
    public function getHeaderText()
    {
        if( Mage::registry('package_data') && Mage::registry('package_data')->getId() ) {
            return Mage::helper('manager')->__("106.header.edit-package", $this->htmlEscape(Mage::registry('package_data')->getName()));
        } else {
            return Mage::helper('manager')->__('106.header.add-package');
        }
    }
}