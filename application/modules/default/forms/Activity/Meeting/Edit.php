<?php
/**
 * Form to edit meeting entry
 *
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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
 
class Core_Form_Activity_Meeting_Edit extends Core_Form_Activity_Meeting_Create
{
    protected $_meetingModel;

    public function __construct($meetingModel)
    {
            $this->_meetingModel = $meetingModel;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $formValues = $this->_meetingModel->fetch();
        $startTime = new Zend_Date($formValues['start_date']);
        $endTime = new Zend_Date($formValues['end_date']);
        $formValues['start_time'] = 'T' . $startTime->get(Zend_Date::HOUR) . ':' . $startTime->get(Zend_Date::MINUTE) . ':'
                                    . $startTime->get(Zend_Date::SECOND);
        $formValues['end_time'] = 'T' . $endTime->get(Zend_Date::HOUR) . ':' . $endTime->get(Zend_Date::MINUTE) . ':'
                                    . $endTime->get(Zend_Date::SECOND);

        Zend_Date::setOptions(array('format_type' => 'php'));
        $startDate = new Zend_Date($formValues['start_date']);
        $formValues['start_date'] = $startDate->toString('Y-m-d');
        $endDate = new Zend_Date($formValues['end_date']);
        $formValues['end_date'] = $endDate->toString('Y-m-d');

        $this->populate($formValues);

    }    
}


