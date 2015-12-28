<?php

/**
 * Faett_Manager_Block_Messages
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
class Faett_Manager_Block_Messages extends Mage_Core_Block_Messages
{

    /**
     * Adding new console message.
     *
     * @param string $message The message to add
     * @return Faett_Manager_Block_Messages The block instance
     */
    public function addConsole($message)
    {
        $this->addMessage(
            Mage::getSingleton('manager/message')->console(
                $message
            )
        );
        return $this;
    }

    /**
     * Retrieve messages in HTML format grouped by type.
     *
     * @return string The HTML with the messages
     */
    public function getGroupedHtml()
    {

        $types = array(
            Mage_Core_Model_Message::ERROR,
            Mage_Core_Model_Message::WARNING,
            Mage_Core_Model_Message::NOTICE,
            Mage_Core_Model_Message::SUCCESS,
            // adding new console message type
            Faett_Manager_Model_Message::CONSOLE
        );

        $html = '';

        foreach ($types as $type) {

            if ($messages = $this->getMessages($type)) {

                if (!$html) {
                    $html .= '<ul class="messages">';
                }

                $html .= '<li class="' . $type . '-msg">';
                $html .= '<ul>';

                foreach ($messages as $message) {
                    $html.= '<li>';
                    $html.= $message->getText();
                    $html.= '</li>';
                }

                $html .= '</ul>';
                $html .= '</li>';
            }
        }

        if ($html) {
            $html .= '</ul>';
        }

        return $html;
    }
}