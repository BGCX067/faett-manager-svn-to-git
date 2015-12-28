<?php

/**
 * Faett_Manager_Package_Information_Abstract
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
abstract class Faett_Manager_Package_Information_Abstract
    implements Faett_Manager_Package_Interfaces_Information {

    /**
     * The package itself.
     * @var Faett_Manager_Model_Package
     */
    protected $_package = null;

    /**
     * The package identifier.
     * @var string
     */
    protected $_identifier = '';

    /**
     * (non-PHPdoc)
     * @see lib/TechDivision/Licenceclient/Package/Interfaces/Faett_Manager_Package_Interfaces_Information#getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * (non-PHPdoc)
     * @see lib/TechDivision/Licenceclient/Package/Interfaces/Faett_Manager_Package_Interfaces_Information#getPackage()
     */
    public function getPackage()
    {
        return $this->_package;
    }
}