<?php

/**
 * Faett_Manager_Adminhtml_AbstractController
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

// set the error reporting to avoid strict PEAR related warnings
error_reporting(E_ALL & ~E_NOTICE);

// include the necessary libraries
require_once 'PEAR.php';
require_once 'PEAR/Frontend.php';
require_once 'PEAR/Config.php';
require_once 'PEAR/Command.php';
require_once 'PEAR/Command/Remote.php';
require_once 'Faett/Core/Factory.php';

/**
 * @todo
 * 		Bugfix for PEAR version v1.6.2, has to be removed for a new PEAR version
 */
require_once 'PEAR/PackageFile/v2.php';
require_once 'PEAR/PackageFile/v2/Validator.php';

/**
 * @category   	Faett
 * @package    	Faett_Manager
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
abstract class Faett_Manager_Adminhtml_AbstractController
    extends Mage_Adminhtml_Controller_Action {

    /**
     * Method is called by the controller before the requested action is
     * invoked.
     *
     * This method checks if a valid licence for the package is available,
     * if not the user is redirected to the package detail page to enter
     * a valid serialz.
     *
     * @return Mage_Core_Controller_Front_Action The controller instance
     */
    public function preDispatch()
    {
        try {
            // invoke the parent's preDispatch method
            parent::preDispatch();
            // validate the package information
            Mage::getModel('manager/connector')->validate(
                $packageInformation = $this->_getPackageInformation()
            );
            // return the instance
            return $this;
        } catch(Faett_Manager_Exceptions_InvalidLicenceException $ile) {
            // log the exception
            Mage::logException($ile);
            // add an error to the session
        	Mage::getSingleton('adminhtml/session')->addError(
        	    Mage::helper('manager')->__('Please enter a valid serial number')
        	);
            // redirect and request user to enter a valid Serialz
            $this->_forward(
            	'edit', 
            	'adminhtml_package', 
            	'manager', 
            	array(
			    	'id' => $packageInformation->getPackage()->getId()
			    )
            ); 
        } catch(Faett_Manager_Exceptions_ChannelLoginException $cle) {
            // log the exception
            Mage::logException($cle);
            // add an error to the session
        	Mage::getSingleton('adminhtml/session')->addError(
        	    Mage::helper('manager')->__('Invalid channel login data specified')
        	);
            // redirect and request user to enter a valid Serialz
            $this->_forward(
            	'index', 
            	'adminhtml_channel', 
            	'manager', 
            	array(
			    	'id' => $packageInformation->getPackage()->getId()
			    )
            );
        } catch(Faett_Manager_Exceptions_ChannelNotFoundException $cnfe) {
            // log the exception
            Mage::logException($cnfe);
            // add an error to the session
        	Mage::getSingleton('adminhtml/session')->addError(
        	    Mage::helper('manager')->__($cnfe->getMessage())
        	);
            // redirect and request user to register channel first
            $this->_forward(
            	'new', 
            	'adminhtml_channel', 
            	'manager'
            );
        }
    }

    /**
     * Abstract method to return the package's unique identifier.
     *
     * @return string the package's unique identifier
     */
    protected abstract function _getIdentifier();

    /**
     * Details package information with Name, Channel and Serialz.
     *
     * @return Faett_Manager_Package_Interfaces_Information
     */

    protected function _getPackageInformation()
    {
        return Faett_Manager_Package_Information_Default::create()->init(
            $this->_getIdentifier()
        );
    }
}