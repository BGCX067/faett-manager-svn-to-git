<?php

/**
 * Faett_Manager_Helper_Data
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
class Faett_Manager_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * The default package state.
     * @var integer
     */
    const PACKAGE_STATE_NOT_INSTALLED = 0;

    /**
     * The package state for installed packages.
     * @var integer
     */
    const PACKAGE_STATE_INSTALLED = 1;

    /**
	 * The package state for packages with an update available.
     * @var integer
     */
    const PACKAGE_STATE_UPDATE_AVAILABLE = 2;

    /**
     * The session key for the selected channel.
     * @var string
     */
    const CHANNEL_ID = 'channel_id';

    /**
     * The registry key to store the model version under.
     * @var string
     */
    const MODEL_VERSION = "model/version";

    /**
     * The registry key to store the shop version under.
     * @var string
     */
    const SHOP_VERSION = "shop/version";

    /**
     * The default package state.
     *
     * Packages are by default not installed and the
     * default value is therefore 0.
     *
     * @return integer The default package state, 0 by default
     */
    public function getDefaultPackageState()
    {
        return Faett_Manager_Helper_Data::PACKAGE_STATE_NOT_INSTALLED;
    }

    /**
	 * Returns the state of the passed package instance.
	 *
	 * The state can be one of not installed, installed or
	 * update available.
	 *
	 * If the package is not installed in the local PEAR repository the
	 * state is not installed. Depending if the package is installed and
	 * if a new version can be installed the package state is installed
	 * or update available.
	 *
	 * @param Faett_Manager_Model_Package $package
	 * 		The package to return the state for
	 * @return string The package state
     */
	public function getPackageState(Faett_Manager_Model_Package $package) {
        // load the installed and the available version
	    $versionInstalled = $package->getVersionInstalled();
	    $versionLatest = $package->getVersionLatest();
        // return the actual package state
        if (empty($versionInstalled)) {
            return Faett_Manager_Helper_Data::PACKAGE_STATE_NOT_INSTALLED;
        } elseif (strcmp($versionInstalled, $versionLatest) < 0) {
            return Faett_Manager_Helper_Data::PACKAGE_STATE_UPDATE_AVAILABLE;
        } else {
            return Faett_Manager_Helper_Data::PACKAGE_STATE_INSTALLED;
        }
	}

    /**
	 * Returns the channel of the passed package as
	 * string, formatted for package installation.
	 *
	 * @param Faett_Manager_Model_Package $package
	 * 		The package to return the formatted channel for
	 * @param string $schema The schema to prepend the channel string with
	 * @return string The formatted channel
     */
    public function getChannelAsString(
        Faett_Manager_Model_Package $package,
        $schema = 'channel://') {
	        // concatenate the and return the channel
            return
                $schema .
    	        $package->getChannel()->getUrl() . '/' .
    	        $package->getName();
    }

    /**
     * This method calculates a model version number by summarizing the
     * latest version of all extension affecting the database model.
     *
     * @return string
     */
    public function calculateModelVersion()
    {
        // intialize the array with the version information
        $modelVersion = array();
        // calculate the version number
        foreach (Mage::getModel('manager/resource')->getCollection() as $resource) {
            foreach (explode('.', $resource->getVersion()) as $key => $version) {
                $modelVersion[$key] = $modelVersion[$key] + $version;
            }
        }
        // concatenate and return the version number
        return implode('.', $modelVersion);
    }

    /**
     * This method calculates a shop version number by summarizing
     * the latest version of all installed extensions.
     *
     * @return string
     */
    public function calculateShopVersion()
    {
        // initialize the array with the version information
        $shopVersion = array();
        // calculate the version number
        foreach ($modules = Mage::getConfig()->getModuleConfig()->asArray() as $name => $module) {
            foreach (explode('.', $module['version']) as $key => $version) {
                $shopVersion[$key] = $shopVersion[$key] + $version;
            }
        }
        // concatenate and return the version number
        return implode('.', $shopVersion);
    }
}