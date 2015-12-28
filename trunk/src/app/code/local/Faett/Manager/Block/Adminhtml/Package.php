<?php

/**
 * Faett_Manager_Block_Adminhtml_Package
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
class Faett_Manager_Block_Adminhtml_Package
    extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup = 'manager';
        $this->_controller = 'adminhtml_package';

        $this->_headerText =
            Mage::helper('manager')->__(
            	'500.header.manager-packages',
                $this->_getHelper()->calculateShopVersion(),
                $this->_getHelper()->calculateModelVersion()
            );

        parent::__construct();

        $this->_addRefreshButton();
        $this->_removeButton('add');
    }

    protected function _getHelper()
    {
        return Mage::helper('manager');
    }

    protected function _addRefreshButton()
    {
        $this->_addButton(
        	'refresh', array(
                'label' => Mage::helper('manager')->__('109.header.button.label.refresh'),
                'onclick' => "$('loading-mask').show();setLocation('{$this->getUrl(
                	'manager/adminhtml_package/refresh'
                )}')",
                'class' => 'scalable',
            )
        );
    }
}