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

// add missing include path for faett managing
$f = base64_decode('YXBwL01hZ2UucGhw');
$a = file_get_contents($f);
$e = base64_decode('VmFyaWVuX0F1dG9sb2FkOjpyZWdpc3RlcigpOw==');
$t = str_replace($e,base64_decode("c2V0X2luY2x1ZGVfcGF0aChCUCAuIERTIC4gJ2Rvd25sb2FkZXInIC4gRFMgLiAncGVhcmxpYicgLiBEUyAuICdwaHAnIC4gUFMgLiBnZXRfaW5jbHVkZV9wYXRoKCkpOwo=") . $e, $a);
$t = file_put_contents($f, $t);