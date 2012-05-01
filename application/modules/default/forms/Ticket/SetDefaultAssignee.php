<?php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation,  version 3 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending 
 * an electronic mail 
 * to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @category    BizSense
 * @package     Core
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd.
 * @version    $Id:$
 */

/**
 *
 * @category    BizSense
 * @package     Core
 * @subpackage  Core_Model
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt Ltd
 */
class Core_Form_Ticket_SetDefaultAssignee extends Zend_Form
{
    public function init()
    {
        $userElementSource = new Core_Form_Ticket_Element_AssignedTo();
        $user = $userElementSource->getElement();
        $user->setRequired(true);
        $this->addElement($user);

        $this->addElement('checkbox', 'set_unassigned', 
            array(
                'label' => 'Assign to all unassigned tickets',
            )
        );

        $this->addElement('submit', 'submit', array
               (
                'class' => 'submit_button'
               )
             );
            
    }
}
