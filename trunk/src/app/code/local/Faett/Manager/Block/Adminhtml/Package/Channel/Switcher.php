<?php
/**
 * Faett_Manager_Block_Adminhtml_Package_Channel_Switcher
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
 * Store switcher block.
 * 
 * @category   	Faett
 * @package    	Faett_Manager
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Manager_Block_Adminhtml_Package_Channel_Switcher
    extends Mage_Adminhtml_Block_Template {

    /**
     * @var array
     */
    protected $_channelIds;

    /**
     * @var bool
     */
    protected $_hasDefaultOption = true;

    public function __construct()
    {
        parent::__construct();
        // $this->setTemplate('manager/channel/switcher.phtml');
        $this->setUseConfirm(true);
        $this->setUseAjax(false);
        $this->setDefaultChannelName($this->__('All Channels'));
    }

    /**
     * Get channels
     *
     * @return array
     */
    public function getChannels()
    {
        $channels = Mage::getModel('manager/channel')->getCollection();

        if ($channelIds = $this->getChannelIds()) {
            foreach ($channels as $channel) {
                if (!in_array($channelId = $channel->getId(), $channelIds)) {
                    unset($channels[$channelId]);
                }
            }
        }

        return $channels;
    }

    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('*/*/*', array('_current' => true, 'channel' => null));
    }

    public function getChannelId()
    {
        $channelId = $this->getRequest()->getParam('channel');
        if (empty($channelId)) {
            $channelId = Mage::getSingleton('adminhtml/session')->getData(
                Faett_Manager_Helper_Data::CHANNEL_ID
            );
        }
        return $channelId;
    }

    public function setChannelIds($channelIds)
    {
        $this->_channelIds = $channelIds;
        return $this;
    }

    public function getChannelIds()
    {
        return $this->_channelIds;
    }

    /**
     * Set/Get whether the switcher should show default option
     *
     * @param bool $hasDefaultOption
     * @return bool
     */
    public function hasDefaultOption($hasDefaultOption = null)
    {
        if (null !== $hasDefaultOption) {
            $this->_hasDefaultOption = $hasDefaultOption;
        }
        return $this->_hasDefaultOption;
    }
}
