<?php

/**
 * Faett_Manager_Model_Channel
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
class Faett_Manager_Model_Channel
    extends Mage_Core_Model_Abstract {

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('manager/channel');
    }

    /**
     * Load channel by its alias.
     *
     * @param string $alias The alias of the channel to load
     * @return Faett_Manager_Model_Channel
     * 		The channel itself
     */
    public function loadByAlias($alias)
    {
        $this->_getResource()->loadByAlias($this, $alias);
        return $this;
    }

    /**
     * Load channel for the passed package.
     *
     * @param Faett_Manager_Model_Package $package
     * 		The package to load the channel for
     * @return Faett_Manager_Model_Channel
     * 		The channel itself
     */
    public function loadByPackage(
        Faett_Manager_Model_Package $package) {
        $this->load($package->getChannelIdFk());
        return $this;
    }

    /**
     * get password hash
     *
     * @param string $password The plain password
     * @return string The hashed password
     */
    public function hashPassword($password, $salt=null)
    {
        return Mage::helper('core')->getHash($password, !is_null($salt) ? $salt : 2);
    }
}