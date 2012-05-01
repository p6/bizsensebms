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
 * an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
Class Core_Form_Status_Log extends Zend_Form
{
    /*
     * @return Zend_Form
     */
    public function init()
    {
        $dateFrom = new Zend_Dojo_Form_Element_DateTextBox('date_from');
        $dateFrom->setLabel('Date From');
        $dateFrom->setFormatLength('long')
                    ->setInvalidMessage('Invalid date'); 
        $this->addElement($dateFrom);

        $dateTo = new Zend_Dojo_Form_Element_DateTextBox('date_to');
        $dateTo->setLabel('Date To');
        $dateTo->setFormatLength('long')
                    ->setInvalidMessage('Invalid date');
        $this->addElement($dateTo);        

        $startTime = new Zend_Dojo_Form_Element_TimeTextBox('start_time');
        $startTime->setLabel('Time From');
        $this->addElement($startTime);

        $endTime = new Zend_Dojo_Form_Element_TimeTextBox('end_time');
        $endTime->setLabel('Time To');
        $this->addElement($endTime);

        $this->addElement('submit', 'submit', array
            (
                'label' => 'Submit',
                'ignore' => true,
                'class' => 'submit_button'
            )
        );
    }
}

