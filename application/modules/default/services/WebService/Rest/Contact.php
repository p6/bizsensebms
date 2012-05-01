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
class Core_Service_WebService_Rest_Contact
{
    /**
     * @var object Core_Model_Contact
     */
    protected $_contactModel;

    /**
     * @var string the work email
     */
    protected $_workEmail;

    /**
     * @return object Core_Model_Contact
     */
    public function getContactModel()
    {
        if (!$this->_contactModel) {
            $this->_contactModel = new Core_Model_Contact;
        }
        return $this->_contactModel;
    }

    /**
     * @param string $email the work email of the contact
     * @return fluent interface
     */
    public function setWorkEmail($email)
    {
        $this->_workEmail = $email;
        return $this;
    }

    /**
     * @param array $data
     * @return bool 
     */
    public function updatePassword(array $data)
    {
       $table = $this->getContactModel()->getTable();
       $where = $table->getAdapter()
                    ->quoteInto(
                        'work_email = ? and ss_enabled = 1', 
                        $this->_workEmail);
       $dataToUpdate['ss_password'] = md5($data['password']);
       $result = $table->update($dataToUpdate, $where);
       return $result;
    }

    /**
     * @return array the contact record
     */
    public function fetch()
    {
       return $this->getContactModel()->findByWorkEmail($this->_workEmail);
    }
}
