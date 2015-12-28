<?php

/**
 * Faett_Manager_Model_Package
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
class Faett_Manager_Model_Package
    extends Mage_Core_Model_Abstract {

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('manager/package');
    }

    /**
	 * Returns the last installed release.
	 *
	 * @return Faett_Manager_Model_Release The last installed release
     */
    public function getInstalledRelease()
    {
        return Mage::getModel('manager/release')->loadByPackageInstalled(
            $this
        );
    }

    /**
     * The channel the package belongs to.
     *
     * @return Faett_Manager_Model_Channel The package channel
     */
    public function getChannel()
    {
        return Mage::getModel('manager/channel')->loadByPackage($this);
    }

    /**
     * Load package by its name.
     *
     * @param string $packageName The name of the package to load
     * @return Faett_Manager_Model_Package
     * 		The package itself
     */
    public function loadByName($packageName)
    {
        $this->_getResource()->loadByName($this, $packageName);
        return $this;
    }

    /**
     * Load package by its channel and name.
     *
     * @param string $channel The channel name of the package to load
     * @param string $packageName The name of the package to load
     * @return Faett_Manager_Model_Package
     * 		The package itself
     */
    public function loadByChannelAndName($channel, $packageName)
    {
        $this->_getResource()->loadByChannelAndName($this, $channel, $packageName);
        return $this;
    }
}