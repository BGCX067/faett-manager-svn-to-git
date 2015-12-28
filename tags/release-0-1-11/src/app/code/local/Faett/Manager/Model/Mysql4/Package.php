<?php

/**
 * Faett_Manager_Model_Mysql4_Package
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
class Faett_Manager_Model_Mysql4_Package
    extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        // Note that the package_id refers to the key field in your database table.
        $this->_init('manager/package', 'package_id');
    }

    /**
     * Load the package by its name.
     *
     * @param Faett_Manager_Model_Package $package The package to initialize
     * @param string $packageName The name of the package to load
     * @return void
     */
    public function loadByName($package, $packageName)
    {
        // initialize the SQL for loading the package by its name
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('manager/package'), array($this->getIdFieldName()))
            ->where('name=:name');
        // the parameters to bind to the SQL statement
        $params = array('name' => $packageName);
		// try to load the package by its name
        if ($id = $this->_getReadAdapter()->fetchOne($select, $params)) {
            // use the found data to initialize the instance
            $this->load($package, $id);
        }
    }

    /**
     * Load the package by its channel and package name.
     *
     * @param Faett_Manager_Model_Package $package The package to initialize
     * @param string $channel The channel name of the package to load
     * @param string $packageName The name of the package to load
     * @return void
     */
    public function loadByChannelAndName($package, $channel, $packageName)
    {
        // initialize the SQL for loading the requested package
        $select = "SELECT {$this->getIdFieldName()} " .
                  "FROM {$this->getTable('manager/package')} t1, " .
                  "     {$this->getTable('manager/channel')} t2 " .
                  "WHERE t2.alias = :channel " .
                  "AND t1.channel_id_fk = t2.channel_id " .
                  "AND t1.name = :packageName";
        // the parameters to bind to the SQL statement
        $params = array(
        	'channel' => $channel,
        	'packageName' => $packageName
        );
		// try to load the package by its name
        if ($id = $this->_getReadAdapter()->fetchOne($select, $params)) {
            // use the found data to initialize the instance
            $this->load($package, $id);
        }
    }


    /**
     * Loads and returns the channel of the package.
     *
     * @return Faett_Package_Model_Channel The package's channel
     */
    public function getChannel()
    {
        return Mage::getModel()->load(
            $this->getChannelIdFk()
        );
    }
}