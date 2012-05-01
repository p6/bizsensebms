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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
class Ticket_SettingsController extends Zend_Controller_Action
{
    protected $_model;

    public function init()
    {
        $this->_model = new Core_Model_Ticket();
    }

    public function defaultassigneeAction()
    {
        $form = new Core_Form_Ticket_SetDefaultAssignee();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->setDefaultAssigneeId(
                    $form->getValue('assigned_to')
                );
                if ($form->getValue('set_unassigned') == 1) {
                    $this->_model->assignUnassignedTicketsTo(
                        $form->getValue('assigned_to')
                    );
                }
                $this->view->message = 'Settings successfully saved';
            } else {
                $form->populate($this->getRequest()->getPost());
            } 
        } else {
            $form->populate(
                array('assigned_to' => $this->_model->getDefaultAssigneeId())
            );
        }

    }
}
