<?php

/**
 * Faett_Manager_Block_Adminhtml_Channel_Grid
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
class Faett_Manager_Block_Adminhtml_Channel_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('channelGrid');
        $this->setDefaultSort('channel_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        // initialize the Collection with the available channels
        $collection = Mage::getModel('manager/channel')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('name', array(
                'header'    => Mage::helper('manager')->__('103.grid.header.url'),
                'align'     =>'left',
                'index'     => 'url',
            )
        );

        $this->addColumn('summary', array(
    			'header'    => Mage::helper('manager')->__('103.grid.header.alias'),
    			'width'     => '470px',
    			'index'     => 'alias',
            )
        );

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('manager')->__('103.grid.header.action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                        array(
                            'caption'   => Mage::helper('manager')->__('103.grid.header.action.edit'),
                            'url'       => array('base'=> '*/*/edit'),
                            'field'     => 'id'
                        ),
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
        $this->setMassactionIdField('channel_id');
        $this->getMassactionBlock()->setFormFieldName('channels');

        $this->getMassactionBlock()->addItem('delete', array(
        		'label'    => Mage::helper('manager')->__('103.grid.massaction.label.delete'),
                'url'      => $this->getUrl('*/*/massDelete'),
                'confirm'  => Mage::helper('manager')->__('103.grid.massaction.confirm')
            )
        );

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}