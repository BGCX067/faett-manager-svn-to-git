<?php

/**
 * Faett_Manager_Block_Adminhtml_Package_Grid
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
class Faett_Manager_Block_Adminhtml_Package_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('packageGrid');
        $this->setDefaultSort('package_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        // initialize the Collection with the available packages
        $collection = Mage::getModel('manager/package')->getCollection();
        // load the ID of the select channel from the session
        $channelId = Mage::getSingleton('adminhtml/session')->getData(
		    Faett_Manager_Helper_Data::CHANNEL_ID
		);
        // log a message with the channel ID found in the session
		Mage::log('Found channel with ID ' . $channelId . ' in session');
		// if a channelId was found in the session add a filter for it
        if (!empty($channelId)) {
            $collection->addFieldToFilter('channel_id_fk', $channelId);
        }
        // load and set the collection
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        // initialize the PEAR service implementation
        $service = Faett_Core_Factory::get(Mage::getBaseDir());
        // iterate over the available packages
        foreach($this->getCollection() as $package) {
            // load the packagename and the channel's alias
            $packageName = $package->getName();
            $alias = $package->getChannel()->getAlias();
            // check if the package is already installed
            if ($service->packageExists($packageName, $alias)) {
                $package->setState(
                    Mage::helper('manager')->getPackageState($package)
                );
            }
        }
        // return the Grid itself
        return $this;
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
                'header'    => Mage::helper('manager')->__('107.grid.header.name'),
                'align'     =>'left',
                'index'     => 'name',
            )
        );

        $this->addColumn('summary', array(
    			'header'    => Mage::helper('manager')->__('107.grid.header.summary'),
    			'width'     => '470px',
    			'index'     => 'summary',
            )
        );

        $this->addColumn('version_installed', array(
             'header'    => Mage::helper('manager')->__('107.grid.header.installed'),
             'align'     =>'right',
             'width'     => '30px',
             'index'     => 'version_installed',
             )
         );

        $this->addColumn('version_latest', array(
             'header'    => Mage::helper('manager')->__('107.grid.header.available'),
             'align'     =>'right',
             'width'     => '30px',
             'index'     => 'version_latest',
             )
         );

        $this->addColumn('state', array(
                'header'    => Mage::helper('manager')->__('107.grid.header.state'),
                'align'     => 'left',
                'width'     => '80px',
                'index'     => 'state',
                'type'      => 'options',
                'options'   => array(
                    0 => Mage::helper('manager')->__('107.grid.header.state.not-installed'),
                    1 => Mage::helper('manager')->__('107.grid.header.state.installed'),
                    2 => Mage::helper('manager')->__('107.grid.header.state.updateable'),
                ),
            )
        );

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('manager')->__('107.grid.header.action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                        array(
                            'caption'   => Mage::helper('manager')->__('107.grid.header.action.info'),
                            'url'       => array('base'=> '*/*/edit'),
                            'field'     => 'id'
                        ),
                        array(
                            'caption'   => Mage::helper('manager')->__('107.grid.header.action.install'),
                            'url'       => array('base'=> '*/*/install'),
                            'field'     => 'id'
                        ),
                        array(
                            'caption'   => Mage::helper('manager')->__('107.grid.header.action.uninstall'),
                            'url'       => array('base'=> '*/*/uninstall'),
                            'field'     => 'id'
                        ),
                        array(
                            'caption'   => Mage::helper('manager')->__('107.grid.header.action.upgrade'),
                            'url'       => array('base'=> '*/*/upgrade'),
                            'field'     => 'id'
                        )
                    ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            )
        );

        $this->addExportType('*/*/exportCsv', Mage::helper('manager')->__('900.grid.export.type.csv'));
        $this->addExportType('*/*/exportXml', Mage::helper('manager')->__('900.grid.export.type.xml'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('package_id');
        $this->getMassactionBlock()->setFormFieldName('manager');

        $this->getMassactionBlock()->addItem('delete', array(
        		'label'    => Mage::helper('manager')->__('107.grid.massaction.label.delete'),
                'url'      => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('manager')->__('107.grid.massaction.confirm')
            )
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}