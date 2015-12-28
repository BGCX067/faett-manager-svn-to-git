<?php

/**
 * Faett_Manager_Model_Connector
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
class Faett_Manager_Model_Connector
    extends Mage_Core_Model_Abstract {

    /**
     * The session key with the flag that a valid licence exists or not.
     * @var string
     */
    const HAS_VALID_LICENCE = 'hasValidLicence';
    
    const CHANNEL_VALIDATE = 'channel.validate';
    	
    /**
     * The PEAR service implementation to use
     * @var Faett_Core_Interfaces_Service
     */
    private $_service = null;

    /**
     * The console log stream.
     * @var resource
     */
    private $_logStream = null;

    /**
     * (non-PHPdoc)
     * @see lib/Varien/Varien_Object#_construct()
     */
    public function _construct()
    {
        // initialize the model
        parent::_construct();
        $this->_init('manager/connector');
        // initialize the PEAR service implementation
        $this->_service = Faett_Core_Factory::get(Mage::getBaseDir());
        // initialize the console log stream in 'html' mode
        $this->_service->getUI()->setLogStream(
            $this->_logStream = fopen('php://memory', 'w+')
        );
    }

    /**
	 * Return the username of the channel, registered by Faett_Manager
	 * in the Magento internal PEAR repository.
	 *
	 * @param Faett_Manager_Package_Interfaces_Information
	 * 		The package information to return the username for
	 * @return string The username for the channel
     */
    protected function _getUsername(
        Faett_Manager_Package_Interfaces_Information $information) {
        return $this->_service->getUsername(
            $url = $this->_getChannel($information)->getUrl()
        );
    }

    /**
	 * Return the password of the channel, registered by Faett_Manager
	 * in the Magento internal PEAR repository.
	 *
	 * @param Faett_Manager_Package_Interfaces_Information
	 * 		The package information to return the password for
	 * @return string The username for the channel
     */
    protected function _getPassword(
        Faett_Manager_Package_Interfaces_Information $information) {
        return $this->_service->getPassword(
            $this->_getChannel($information)->getUrl()
        );
    }

    /**
	 * Returns the channel for the passed package information.
	 *
	 * @param Faett_Manager_Package_Interfaces_Information
	 * 		The package information to return the channel for
	 * @return Faett_Channel_Model_Channel The requested channel instance
	 * @throws Faett_Manager_Exceptions_ChannelNotFoundException
	 * 		Is thrown if the channel of the package identifier was not registered
     */
    protected function _getChannel(
        Faett_Manager_Package_Interfaces_Information $information) {
        list($alias, $packageName) = explode('/', $information->getIdentifier());
        $channel = Mage::getModel('manager/channel')->loadByAlias($alias);
        // check if a channel for the package identifier was found
        if ($channel->getId() == null) {
        	throw Faett_Manager_Exceptions_ChannelNotFoundException::create(
        		'Channel for alias ' . $alias . ' not registered'
        	);
        }
        // return the channel
        return $channel;
    }

    /**
	 * The URL of the channel for the passed package information.
	 *
	 * @param Faett_Manager_Package_Interfaces_Information
	 * 		The package information to return the channel's URL for
	 * @return string the channel's URL
     */
    protected function _getChannelUrl(
        Faett_Manager_Package_Interfaces_Information $information) {
        return 'http://' . $this->_getChannel($information)->getUrl() . '/api/soap/?wsdl';
    }

    /**
     * Validates the licence entered by the customer in the
     * extensions system configuration tab.
     *
     * @param Faett_Manager_Package_Interfaces_Information
     * 		$information The package information to validate the licence for
     * @return void
     * @throws Faett_Manager_Exceptions_InvalidLicenceException
     * 		Is thrown if the licence is not valid
     */
    public function validate(
        Faett_Manager_Package_Interfaces_Information $information) {
        // create a valid session key for the package
        $key = str_replace('/', '.', $information->getIdentifier());
        // load the sesssion
        $sess = Mage::getSingleton('adminhtml/session');
        // check if a valid licence is available
        if ($data = $sess->getData($key)) {
    		// check if a valid licence was found
	        if (array_key_exists(self::HAS_VALID_LICENCE, $data) && 
	            $data[self::HAS_VALID_LICENCE]) {
	        	// if yes, return without doing anything
	        	return;
	        }
        }
        // initialize the SOAP client
        $client = new Zend_Soap_Client(
            $channelUrl = $this->_getChannelUrl($information)
        );
        
        try {
            // login to webservice
            $session = $client->login(
                $username = $this->_getUsername($information),
                $password = $this->_getPassword($information)
            );
        } catch(Exception $e) {
        	// if the channel login failed throw an exception
            throw Faett_Manager_Exceptions_ChannelLoginException::create(
            	'Channel login failed for validating package ' . $information->getIdentifier()
            );
        }
        // split the identifier into alias and package name
        list($alias, $packageName) = explode(
        	'/',
            $information->getIdentifier()
        );
        // check if the licence is valid
        $isValid = $client->call(
            $session,
            Faett_Manager_Model_Connector::CHANNEL_VALIDATE,
            array(
            	$serialz = $information->getSerialz(),
            	$alias,
            	array(
            		'packageName' => $packageName
            	)
            )
        );
        // if a valid licence was found set it in the session
        if ($isValid) {
        	$sess->setData($key, array(self::HAS_VALID_LICENCE => true));
        } else {
        	// if the licence is not valid throw an exception
            throw Faett_Manager_Exceptions_InvalidLicenceException::create(
            	'Serialz ' . $serialz . ' is not valid for package ' . $information->getIdentifier()
            );
        }
    }

    /**
     * This method requests information of the package
     * with the passed informations.
     *
     * @param Faett_Manager_Package_Interfaces_Information $information
     * 		The requested package
     * @return array The requested info
     */
    public function info(
        Faett_Manager_Package_Interfaces_Information $information) {
        // initialize the SOAP client
        $client = new Zend_Soap_Client(
            $this->_getChannelUrl($information)
        );
        // login to webservice
        $session = $client->login(
            $this->_getUsername($information),
            $this->_getPassword($information)
        );
        // intialize the additional attributes of the package
        $attr = new stdClass();
        $attr->attributes = array(
        	'licence',
            'licence_uri',
            'short_description',
            'description'
        );
        // split the identifier into alias and package name
        list($alias, $packageName) = explode(
        	'/',
            $information->getIdentifier()
        );
        // request the package information
        return $client->call(
            $session,
            'licenceserver.info',
            array(
            	$packageName,
                $alias,
                $attr
            )
        );
    }
}