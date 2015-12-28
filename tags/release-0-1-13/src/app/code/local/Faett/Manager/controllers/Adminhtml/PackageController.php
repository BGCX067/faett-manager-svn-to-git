<?php

/**
 * Faett_Manager_Adminhtml_PackageController
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

require_once 'Faett/Core/Factory.php';

/**
 * @category   	Faett
 * @package    	Faett_Manager
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Manager_Adminhtml_PackageController
    extends Mage_Adminhtml_Controller_Action {

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
     * Initializes the PEAR service implementation.
     *
     * @return void
     */
    public function __construct(
        Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array()) {
        // call the parent constructor
        parent::__construct($request, $response, $invokeArgs);
        // initialize the PEAR service implementation
        $this->_service = Faett_Core_Factory::get(Mage::getBaseDir());
        // initialize the console log stream in 'html' mode
        $this->_service->getUI()->setLogStream(
            $this->_logStream = fopen('php://memory', 'w+')
        );
    }

    /**
     * Writes the log console messages.
     *
     * @return void
     */
    public function __destruct()
    {
        // check if authentication is requested
        if ((boolean) Mage::getStoreConfig('manager/global/debug')) {
            $logStream = trim($this->_getLogStream());
            if (!empty($logStream)) {
                Mage::getSingleton('manager/session')->addConsole(
                    $logStream
        		);
            }
        }
    }

    /**
     * Returns the log stream of the PEAR command excecuted before.
     *
     * @param boolean $asString
     * 		TRUE if a string should be returned,
     * 		FALSE for the stream resource itself
     * @return string|resource The log stream as string or stream resource
     */
    protected function _getLogStream($asString = true)
    {
        if (!$asString) {
            return $this->_logStream;
        }
        rewind($this->_logStream);
        return stream_get_contents($this->_logStream);
    }

    /**
     * Initializes the layout.
     *
     * @return Faett_Manager_Adminhtml_PackageController
     * 		The controller itself
     */
	protected function _initAction()
	{
	    // initialize the layout
		$this->loadLayout()
			->_setActiveMenu('manager/manager')
			->_addBreadcrumb(
			    Mage::helper('manager')->__('201.breadcrumb.manager'),
			    Mage::helper('manager')->__('201.breadcrumb.manager.packages')
	    );
        // return the instance itself
		return $this;
	}

	/**
	 * Returns the path to the configuration section
	 * of the manager.
	 *
	 * @return void
	 */
	protected function _getConfigUrl()
	{
        return $this->getUrl(
            'adminhtml/system_config/edit',
            array(
                'section' => 'manager'
            )
        );
	}

	/**
	 * Returns the package information for the package
	 * with the passed identifier.
	 *
	 * @param string $identifier
	 * @return Faett_Manager_Package_Interfaces_Information
	 * 		The initialized package information
	 */
    protected function _getPackageInformation($identifier)
    {
        return Faett_Manager_Package_Information_Default::create()->init(
            $identifier
        );
    }

	/**
	 * Loads and renders the package overview.
	 *
	 * @return void
	 */
	public function indexAction()
	{
        // load the channel ID from the request
		$channelId = $this->getRequest()->getParam('channel');
        // check if all channels was selected int the channel switcher
		if ($channelId == -1) {
            // if yes, remove the ID of the selected channel from the registry
    		Mage::getSingleton('adminhtml/session')->unsetData(
    		    Faett_Manager_Helper_Data::CHANNEL_ID
    		);
		} elseif (empty($channelId)) {
		    // if not channel ID was passed in the
		    // request use the one from the session
		    $channelId = Mage::getSingleton('adminhtml/session')->getData(
		        Faett_Manager_Helper_Data::CHANNEL_ID
		    );
		} else {
            // if a channel id was passed in the request, attach the ID of
            // the selected channel to the registry
    		Mage::getSingleton('adminhtml/session')->setData(
    		    Faett_Manager_Helper_Data::CHANNEL_ID,
    		    $channelId
    		);
		}
        // render the layout
		$this->_initAction()->renderLayout();
	}

	/**
	 * Refreshes the model with the extension
	 * data from the channel
	 *
	 * @return void
	 */
    public function refreshAction()
	{
	    // set the maximum execution time to endless
        ini_set('max_execution_time', 0);
	    // load the helper and the registry
	    $helper = Mage::helper('manager');
        // initialize the channel model
        $chn = Mage::getModel('manager/channel');
        $collection = $chn->getCollection();
        // load the ID of the select channel from the session
        $channelId = Mage::getSingleton('adminhtml/session')->getData(
		    Faett_Manager_Helper_Data::CHANNEL_ID
		);
        // if a channelId was found in the session add a filter for it
		if (!empty($channelId)) {
		    $collection->addFieldToFilter('channel_id', $channelId);
		}
        // iterate over the channel and load initialize the packages
        foreach ($collection as $c) {
            try {
                // get the URL of the channel to refresh
                $packages = $this->_service->listPackages(
                    $channelName = $c->getUrl()
                );
            } catch(Exception $e) {
                // attach a message to the session
        		Mage::getSingleton('adminhtml/session')->addError(
        		    $helper->__($e->getKey(), $e->getMessage())
        		);
        		// continue with the next channel
                continue;
            }
            // load the the packages
    	    foreach ($packages as $packageName) {
    	        try {
                    // load the package information
        	        $package = $this->_service->packageInfo(
        	            $packageName,
        	            $channelName
        	        );
    	        } catch(Faett_Core_Exceptions_UnknownChannelStateException $e) {
                    // attach a message to the session
            		Mage::getSingleton('adminhtml/session')->addError(
            		    $helper->__($e->getKey(), $e->getMessage())
            		);
            		// continue with the next channel
                    continue;
    	        } catch(Faett_Core_Exceptions_PackageInfoException $e) {
                    // attach a message to the session
            		Mage::getSingleton('adminhtml/session')->addError(
            		    $helper->__($e->getKey(), $e->getMessage())
            		);
            		// continue with the next channel
                    continue;
    	        } catch(Exception $e) {
                    // attach a message to the session
            		Mage::getSingleton('adminhtml/session')->addError(
            		    $e->getMessage()
            		);
            		// continue with the next channel
                    continue;
    	        }
                // initialize the package model
    	        $pkg = Mage::getModel('manager/package');
                // try to load the package from the database by its name
    	        $id = $pkg->loadByName($packageName)->getId();
    	        if (empty($id)) {
    	            // if the package is new, create it
    	            $pkg->setChannelIdFk($c->getId());
        	        $pkg->setName($packageName);
        	        $pkg->setSummary($package['summary']);
        	        $pkg->setVersionInstalled($package['installed']);
        	        if (is_array($package['releases'])) {
        	            $pkg->setVersionLatest(
        	                reset(
        	                    array_keys($package['releases'])
        	                )
        	            );
        	        }
        	        $pkg->setCreatedTime(now());
    				$pkg->setUpdateTime(now());
    	        } else {
    	            // if the package already exists update it
        	        $pkg->setSummary($package['summary']);
        	        $pkg->setVersionInstalled($package['installed']);
        	        if (is_array($package['releases'])) {
        	            $pkg->setVersionLatest(
        	                reset(
        	                    array_keys($package['releases'])
        	                )
        	            );
        	        }
    				$pkg->setUpdateTime(now());
    	        }
                // set the package state
                $pkg->setState($helper->getPackageState($pkg));
                // save the package
    	        $pkg->save();
                // check if the package has releases
    	        if (is_array($package['releases'])) {
                    // load and initialize the releases
        	        foreach ($package['releases'] as $version => $release) {
                        // initialize the release model
            	        $rel = Mage::getModel('manager/release');
                        // try to load the release from the databas by
                        // its package and version
            	        $relId = $rel->loadByPackageAndVersion(
            	            $pkg,
            	            $version
            	        )->getId();
            	        // check if a release was found
            	        if (empty($relId)) {
            	            // if the package is new, create it
            	            $rel->setPackageIdFk($pkg->getId());
                	        $rel->setVersion($version);
                	        $rel->setCreatedTime(now());
            				$rel->setUpdateTime(now());
            	        }
                        // save the release
            	        $rel->save();
        	        }
    	        } else {
                    // attach a message to the session
            		Mage::getSingleton('adminhtml/session')->addError(
            		    $helper->__(
            		        '201.error.package-no-releases', $packageName
            		    )
            		);
    	        }
                // attach a message to the session
        		Mage::getSingleton('adminhtml/session')->addSuccess(
        		    $helper->__(
        		        '201.success.package-update', $packageName
        		    )
        		);
    	    }
            // attach a message to the session
    		Mage::getSingleton('adminhtml/session')->addSuccess(
    		    $helper->__(
    		        '201.success.channel-update', $channel
    		    )
    		);
        }
        // log a message with the message logged by PEAR
        Mage::log($this->_service->getUI()->getLogText());
        // redirect to the licence overview
        $this->_redirect('*/*/');
	}

	public function manualInstallAction()
	{
	    try {
    		// load the helper and the registry
    	    $helper = Mage::helper('manager');
            // initialize the channel model
            $chn = Mage::getModel('manager/channel');
            // load by given channel_id if there is an id
            if ($channel_id = $this->getRequest()->getParam('channel_id')) {
            	// load the channel
                $chn->load($channel_id);
            	// set channel name
            	$channelName = $chn->getUrl();
            	// get package name from post data
            	$packageName = $this->getRequest()->getParam('package_name');
            	// try to get package information for given package_name
            	$package = $this->_service->packageInfo(
            		$packageName,
            	    $channelName
            	);
            	if ($package) {
    	        	// initialize the package model
    	            $pkg = Mage::getModel('manager/package');
    	            // try to load the package from the database by its name
    	            $id = $pkg->loadByName($packageName)->getId();
    	            if (empty($id)) {
    	                // if the package is new, create it
    	                $pkg->setChannelIdFk($chn->getId());
    	                $pkg->setName($packageName);
    	                $pkg->setSummary($package['summary']);
    	                $pkg->setVersionInstalled($package['installed']);
    	                if (is_array($package['releases'])) {
    	                    $pkg->setVersionLatest(
    	                        reset(
    	                            array_keys($package['releases'])
    	                        )
    	                    );
    	                }
    	                $pkg->setCreatedTime(now());
    	    			$pkg->setUpdateTime(now());
    	            } else {
    	                // if the package already exists update it
    	                $pkg->setSummary($package['summary']);
    	                $pkg->setVersionInstalled($package['installed']);
    	                if (is_array($package['releases'])) {
    	                    $pkg->setVersionLatest(
    	                        reset(
    	                            array_keys($package['releases'])
    	                        )
    	                    );
    	                }
    	    			$pkg->setUpdateTime(now());
    	            }
    	             // set the package state
                    $pkg->setState($helper->getPackageState($pkg));
                    // save the package
        	        $pkg->save();
        	        // set package id to request params
        	        $this->getRequest()->setParam('id', $pkg->getId());
        	        $this->_forward('install');
            	}
            } else {
                // add an error message to the session
            	Mage::getSingleton('adminhtml/session')->addError(
                    $helper->__(
                        '201.error.package-no-id'
                    )
                );
            }
	    } catch(Faett_Core_Exceptions_PackageInfoException $pie) {
        	// add an error message to the session
	        Mage::getSingleton('adminhtml/session')->addError(
                $helper->__(
                    '201.error.package-info',
                    $packageName
                )
            );
	    }
        // redirect to the licence overview
        $this->_redirect('*/*/');
	}

	/**
	 * Installs the package with the id passed
	 * in the request.
	 *
	 * @return void
	 */
    public function installAction()
	{
	    try {
    	    // run the install command
    	    $packageName = $this->_runCommand(
    	        $command = Faett_Core_Interfaces_Service::COMMAND_INSTALL
    	    );
            // attach a message to the session
    		Mage::getSingleton('adminhtml/session')->addSuccess(
    		    Mage::helper('adminhtml')->__(
    		        '201.success.package-install', $packageName
    		    )
    		);
	    } catch(Faett_Manager_Exceptions_InvalidCommandException $ice) {
            Mage::getSingleton('adminhtml/session')->addError(
    		    $ice->getMessage()
    		);
	    } catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
    		    Mage::helper('manager')->__(
    		        '900.pear.exception',
    		        $e->getMessage()
    		    )
    		);
	    }
        // redirect to the licence overview
        $this->_redirect('*/*/');
	}

	/**
	 * Uninstalls the package with the id passed
	 * in the request.
	 *
	 * @return void
	 */
    public function uninstallAction()
	{
	    try {
    	    // run the upgrade command
    	    $packageName = $this->_runCommand(
    	        $command = Faett_Core_Interfaces_Service::COMMAND_UNINSTALL
    	    );
            // attach a message to the session
    		Mage::getSingleton('adminhtml/session')->addSuccess(
    		    Mage::helper('adminhtml')->__(
    		        '201.success.package-uninstall', $packageName
    		    )
    		);
	    } catch(Faett_Manager_Exceptions_InvalidCommandException $ice) {
            Mage::getSingleton('adminhtml/session')->addError(
    		    $ice->getMessage()
    		);
	    } catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
    		    Mage::helper('manager')->__(
    		        '900.pear.exception',
    		        $e->getMessage()
    		    )
    		);
	    }
        // redirect to the licence overview
        $this->_redirect('*/*/');
	}

	/**
	 * Upgrades the package with the id passed
	 * in the request.
	 *
	 * @return void
	 */
    public function upgradeAction()
	{
	    try {
    	    // run the upgrade command
    	    $packageName = $this->_runCommand(
    	        $command = Faett_Core_Interfaces_Service::COMMAND_UPGRADE
    	    );
            // attach a message to the session
    		Mage::getSingleton('adminhtml/session')->addSuccess(
    		    Mage::helper('adminhtml')->__(
    		        '201.success.package-upgrade', $packageName
    		    )
    		);
	    } catch(Faett_Manager_Exceptions_InvalidCommandException $ice) {
            Mage::getSingleton('adminhtml/session')->addError(
    		    $ice->getMessage()
    		);
	    } catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
    		    Mage::helper('manager')->__(
    		        '900.pear.exception',
    		        $e->getMessage()
    		    )
    		);
	    }
        // redirect to the licence overview
        $this->_redirect('*/*/');
	}

	/**
	 * Runs the passed PEAR command for the package
	 * with the id passed in the request.
	 *
	 * @param string $command The command to run
	 * @param int $id The id of the package to do something with
	 * @return string The name of the package the command was executed for
	 */
	private function _runCommand($command = 'install')
	{
        // load the ID of the package to initialize and the package itself
        $id = $this->getRequest()->getParam('id');
	    $package = Mage::getModel('manager/package');
	    $package->load($id);
        // initialize the options for the PEAR installer
        $opts = array();
        // initialize the parameters for the PEAR installer
	    $params = array(
	        $config = Mage::helper('manager')->getChannelAsString(
	            $package
	        )
	    );
        // check the passed command
	    switch ($command) {
	        case Faett_Core_Interfaces_Service::COMMAND_INSTALL:
        	    if ((boolean) Mage::getStoreConfig('manager/global/force')) {
                    $opts['force'] = true;
                }
	            $this->_service->install($opts, $params);
	            break;
	        case Faett_Core_Interfaces_Service::COMMAND_UPGRADE:
        	    if ((boolean) Mage::getStoreConfig('manager/global/force')) {
                    $opts['force'] = true;
                }
	            $this->_service->upgrade($opts, $params);
	            break;
	        case Faett_Core_Interfaces_Service::COMMAND_UNINSTALL:
	            $this->_service->uninstall($opts, $params);
	            break;
	        default:
	            throw Faett_Manager_Exception_InvalidCommandException::create(
        		    Mage::helper('manager')->__(
        		        '201.error.invalid-command',
        		        $command,
                        $this->_getConfigUrl()
        		    )
	            );
	    }
        // load the package information
        $info = $this->_service->packageInfo(
            $package->getName(),
            $package->getChannel()->getAlias()
        );
        // load the installed version
        if (array_key_exists('installed', $info)) {
            $package->setVersionInstalled(
                $versionInstalled = $info['installed']
            );
        }
        // load the latest version
        if (is_array($info['releases'])) {
            $package->setVersionLatest(
                $versionLatest = reset(
                    array_keys($info['releases'])
                )
            );
        }
        // set the package state
        $package->setState(
            Mage::helper('manager')->getPackageState($package)
        );
        // update the package
        $package->save();
        // log a message with the message logged by PEAR
        Mage::log($this->_service->getUI()->getLogText());
        // return the package name
        return $package->getName();
	}

	/**
	 * Refreshes the model with the extension
	 * data from the channel
	 *
	 * @return void
	 */
    public function infoAction()
	{
	    // load the helper and the registry
	    $helper = Mage::helper('manager');
	    $registry = $helper->getRegistry();
        // load the ID of the package to initialize and the package itself
	    $id = $this->getRequest()->getParam('id');
	    $package = Mage::getModel('manager/package');
	    $package->load($id);
        // load the package information packagename and the channel's URL
        $info = $this->_packageInfo(
            $package->getName(),
            $package->getChannel()->getUrl()
        );
        // attach a message to the session
		Mage::getSingleton('adminhtml/session')->addSuccess(
		    Mage::helper('manager')->__(
		        var_export($info, true)
		    )
		);
        // redirect to the licence overview
        $this->_redirect('*/*/');
	}

	public function editAction()
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('manager/package')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('package_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('manager/items');

			$this->_addBreadcrumb(
			    Mage::helper('manager')->__('201.breadcrumb.manager'),
			    Mage::helper('manager')->__('201.breadcrumb.manager.packages')
			);

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('manager/adminhtml_package_edit'))
				 ->_addLeft($this->getLayout()->createBlock('manager/adminhtml_package_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(
			    Mage::helper('manager')->__('201.error.package-does-not-exists')
			);
			$this->_redirect('*/*/');
		}
	}

	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('manager/package');

				$model->setId($this->getRequest()->getParam('id'))
					  ->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(
				    Mage::helper('manager')->__('201.success.package-deleted')
				);

				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction()
    {
        $packageIds = $this->getRequest()->getParam('manager');
        if(!is_array($packageIds)) {
			Mage::getSingleton('adminhtml/session')->addError(
			    Mage::helper('manager')->__('201.error.select-package')
			);
        } else {
            try {
                foreach ($packageIds as $packageId) {
                    $manager = Mage::getModel('manager/package')->load($packageId);
                    $manager->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('manager')->__(
                    	'201.success.multi-package-deleted', count($packageIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

	/**
	 * Saves the serialz back to the database.
	 *
	 * @return void
	 */
	public function saveAction()
	{
	    // check if post data exists
		if ($data = $this->getRequest()->getPost()) {
			try {
	            // if yes, load the package
				$model = Mage::getModel('manager/package');
				$model->setData($data)
					  ->setId($this->getRequest()->getParam('id'));
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						  ->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}
                // save the package data
				$model->save();
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        // set an error message that the requested package does not exists
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('manager')->__('200.error.package-id-not-in-request')
        );
        // validate the licence if one is given
        $this->_redirect('*/*/');
	}

    public function exportCsvAction()
    {
        $fileName = 'manager.csv';
        $content = $this->getLayout()->createBlock(
        	'manager/adminhtml_manager_grid'
        )->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'manager.xml';
        $content = $this->getLayout()->createBlock(
        	'manager/adminhtml_manager_grid'
        )->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}