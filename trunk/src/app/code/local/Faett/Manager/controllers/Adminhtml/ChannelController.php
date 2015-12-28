<?php

/**
 * Faett_Manager_Adminhtml_ChannelController
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
require_once 'Faett/Core/Factory.php';

/**
 * @category   	Faett
 * @package    	Faett_Manager
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Manager_Adminhtml_ChannelController
    extends Mage_Adminhtml_Controller_Action {

    /**
     * The PEAR service implementation to use
     * @var Faett_Core_Interfaces_Service
     */
    private $_service = null;

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
    }

    /**
     *
     * Initializes the layout.
     *
     * @return Faett_Manager_Adminhtml_LicencesController
     * 		The controller itself
     */
	protected function _initAction()
	{
		$this->loadLayout()
			->_setActiveMenu('manager/channel')
			->_addBreadcrumb(
			    Mage::helper('manager')->__('200.breadcrumb.manager'),
			    Mage::helper('manager')->__('200.breadcrumb.manager.channels')
	    );
		return $this;
	}

	/**
	 * Loads and renders the package overview.
	 *
	 * @return void
	 */
	public function indexAction()
	{
		$this->_initAction()->renderLayout();
	}

	public function editAction()
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('manager/channel')->load($id);

		if ($model->getId() || $id == 0) {

		    $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('channel_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('manager/channels');

			$this->_addBreadcrumb(
			    Mage::helper('manager')->__('200.breadcrumb.manager'),
			    Mage::helper('manager')->__('200.breadcrumb.manager.channels')
			);


			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('manager/adminhtml_channel_edit'))
				->_addLeft($this->getLayout()->createBlock('manager/adminhtml_channel_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('manager')->__('200.error.channel-does-not-exists'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	/**
	 * Discovers the channel with the ID
	 * passed in the request.
	 *
	 * @return void
	 */
	public function discoverAction()
	{

	    $channel = Mage::getModel('manager/channel');
	    $channel->load($this->getRequest()->getParam('id'));

	    $opts = array();

	    $params = array(
	        $channel->getUrl()
	    );

	    // run the discover channel command
	    $this->_service->channelDiscover($opts, $params);

        // attach a message to the session
		Mage::getSingleton('adminhtml/session')->addSuccess(
		    Mage::helper('adminhtml')->__(
		        '200.success.channel-discovered', $channelName
		    )
		);
        // redirect to the licence overview
        $this->_redirect('*/*/');
	}

	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()) {

			$model = Mage::getModel('manager/channel');
			$model->setData($data)
				  ->setId($this->getRequest()->getParam('id'));

			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						  ->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}

				$model->save();

        	    $opts = array();

        	    $params = array(
        	        $model->getUrl()
        	    );

				$this->_service->channelDiscover($opts, $params);

				Mage::getSingleton('adminhtml/session')->addSuccess(
				    Mage::helper('manager')->__('200.success.channel-saved')
				);

				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('manager')->__('200.error.channel-id-not-in-request')
        );
        $this->_redirect('*/*/');
	}

	public function deleteAction()
	{
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {

				$model = Mage::getModel('manager/channel');

				$model->setId($this->getRequest()->getParam('id'))
					  ->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(
				    Mage::helper('manager')->__('200.success.channel-deleted')
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
        $channelIds = $this->getRequest()->getParam('channels');
        if(!is_array($channelIds)) {
			Mage::getSingleton('adminhtml/session')->addError(
			    Mage::helper('manager')->__('200.error.select-channel')
			);
        } else {
            try {
                foreach ($channelIds as $channelId) {
                    $manager = Mage::getModel('manager/channel')->load($channelId);
                    $manager->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('manager')->__(
                        '200.success.multi-channel-deleted', count($channelIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'channels.csv';
        $content    = $this->getLayout()->createBlock(
        	'manager/adminhtml_manager_grid'
        )->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'channels.xml';
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