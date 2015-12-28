<?php

/**
 * Faett_Manager_Model_Message
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
 * Message model extending the default one for a new console message type.
 *
 * @category   	Faett
 * @package    	Faett_Manager
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Manager_Model_Message extends Mage_Core_Model_Message
{
    /**
     * Key for the console message type
     * @var string
     */
    const CONSOLE = 'console';

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Model/Mage_Core_Model_Message#_factory($code, $type, $class, $method)
     */
    protected function _factory($code, $type, $class='', $method='')
    {
        // check if a console method is requested
        if (strtolower($type == self::CONSOLE)) {
            $message = new Faett_Manager_Model_Message_Console($code);
            $message->setClass($class);
            $message->setMethod($method);
            return $message;
        }
        // call the parent factory method if not a console method
        return parent::_factory($code, $type, $class, $method);
    }

    /**
     * Initializes and returns a new console message.
     *
     * @param $code The message code
     * @param $class The class to set
     * @param $method The method to load
     * @return Faett_Manager_Model_Message The console message instance
     */
    public function console($code, $class='', $method='')
    {
        return $this->_factory($code, self::CONSOLE, $class, $method);
    }
}