<?php

/**
 * Faett_Manager_Model_Mysql4_Release
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
class Faett_Manager_Model_Mysql4_Release
    extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        // Note that the release_id refers to the key field in your database table.
        $this->_init('manager/release', 'release_id');
    }

    /**
     * Loads the release fo the passed package and version.
     *
     * @param Faett_Manager_Model_Release $release The release to load
     * @param Faett_Manager_Model_Package $package
     * 		The package to load the release for
     * @param string $version The version to load the release for
     */
    public function loadByPackageAndVersion(
        Faett_Manager_Model_Release $release,
        Faett_Manager_Model_Package $package,
        $version) {
        // initialize the SQL for loading the package by its name
        $select = $this->_getReadAdapter()->select()
            ->from(
                $this->getTable('manager/release'), array(
                    $this->getIdFieldName()
                )
            )
            ->where('package_id_fk=:packageIdFk')
            ->where('version=:version');
		// try to load the release by its package and version
        if ($id = $this->_getReadAdapter()->fetchOne(
            $select,
            array(
            	'packageIdFk' => $package->getId(),
                'version' => $version
            ))) {
            // use the found data to initialize the instance
            $this->load($release, $id);
        }
    }
}