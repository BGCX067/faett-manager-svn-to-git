<?php

/**
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

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE `{$installer->getTable('manager/channel')}` (
  `channel_id` int(11) unsigned NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  `alias` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$installer->getTable('manager/channel')}`
    (`url`, `alias`, `created_time`, `update_time`) VALUES ('connect.magentocommerce.com/community', 'magento-community', NOW(), NOW());

INSERT INTO `{$installer->getTable('manager/channel')}`
    (`url`, `alias`, `created_time`, `update_time`) VALUES ('www.faett.net', 'faett', NOW(), NOW());

CREATE TABLE `{$installer->getTable('manager/package')}` (
  `package_id` int(11) unsigned NOT NULL auto_increment,
  `channel_id_fk` int(11) unsigned NOT NULL default '1',
  `name` varchar(255) NOT NULL default '',
  `summary` text NOT NULL default '',
  `state` tinyint(1) NOT NULL default '0',
  `version_installed` varchar(10) NOT NULL default '',
  `version_latest` varchar(10) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`package_id`),
  KEY `faett_channel_id_fk` (`channel_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{$installer->getTable('manager/package')}` ADD FOREIGN KEY ( `channel_id_fk` ) REFERENCES `{$installer->getTable('manager/channel')}` (`channel_id`) ON DELETE CASCADE;

CREATE TABLE `{$installer->getTable('manager/release')}` (
  `release_id` int(11) unsigned NOT NULL auto_increment,
  `package_id_fk` int(11) unsigned NOT NULL default '1',
  `version` varchar(10) NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`release_id`),
  KEY `package_id_fk` (`package_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{$installer->getTable('manager/release')}` ADD FOREIGN KEY ( `package_id_fk` ) REFERENCES `{$installer->getTable('manager/package')}` (`package_id`) ON DELETE CASCADE;

");

$installer->endSetup();