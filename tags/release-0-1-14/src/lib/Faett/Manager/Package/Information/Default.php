<?php

/**
 * Faett_Manager_Package_Information_Default
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
class Faett_Manager_Package_Information_Default
    extends Faett_Manager_Package_Information_Abstract {

    /**
     * Proteced constructor to protecte the class against
     * direct instanciaton.
     *
     * @return void
     */
    protected function __construct()
    {
        // nothing to do here
    }

    /**
     * Factory method for creating a new instance.
     *
     * @return Faett_Manager_Package_Information_Default
     * 		The requested instance
     */
    public static function create() {
        return new Faett_Manager_Package_Information_Default();
    }

    /**
     * Initializes the package information and loads
     * the package related to the passed identifier.
     *
     * @param string $identifier
     * 		The package identifier to load the package information for
     * @return Faett_Manager_Package_Information_Default
     * 		The initialized instance
     */
    public function init($identifier) {
        // set the identifier
        $this->_identifier = $identifier;
        // load the package with the necessry information
        list($channel, $packageName) = explode('/', $identifier);
        // load serialz from the system configuration
        $this->_package = Mage::getModel('manager/package')
            ->loadByChannelAndName($channel, $packageName);
        // return the instance itself
        return $this;
    }
}