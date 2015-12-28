<?php

/**
 * Faett_Manager_Model_Mysql4_Channel
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
class Faett_Manager_Model_Mysql4_Channel
    extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * The PEAR service implementation to use
     * @var Faett_Core_Interfaces_Service
     */
    private $_service = null;

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        // Note that the channel_id refers to the key field in your database table.
        $this->_init('manager/channel', 'channel_id');
         // initialize the PEAR service implementation
        $this->_service = Faett_Core_Factory::get(Mage::getBaseDir());
    }

    /**
     * Saves the channel userdata in PEAR config
     *
     * @param Faett_Manager_Model_Channel $channel
     * 		The channel to get Data from
     * @return
     */
    protected function _beforeSave(Faett_Manager_Model_Channel $channel) {
    	// set username and password in pear config if both are set
    	if ($channel->getUsername() && $channel->getNewPassword()) {
    		$this->_service->setUsername($channel->getUsername(), $channel->getUrl());
    		$this->_service->setPassword($channel->getNewPassword(), $channel->getUrl());
    	}
    	// save new password as hash if new password is set
    	if ($channel->getNewPassword()) {
			$channel->setPassword($channel->hashPassword($channel->getNewPassword()));
    	}
    }

    /**
	 * Loads the channel by its alias.
	 *
	 * @param Faett_Manager_Model_Channel $channel
	 * 		The channel to initialize
	 * @param string $alias The alias of the channel to load
	 * @return void
     */
    public function loadByAlias(
        Faett_Manager_Model_Channel $channel,
        $alias) {
        // initialize the SQL for loading the package by its name
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('manager/channel'), array($this->getIdFieldName()))
            ->where('alias=:alias');
		// try to load the package by its name
        if ($id = $this->_getReadAdapter()->fetchOne($select, array('alias' => $alias)))
        {
            // use the found data to initialize the instance
            $this->load($channel, $id);
        }
    }
}