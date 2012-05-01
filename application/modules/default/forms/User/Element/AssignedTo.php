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
class Core_Form_User_Element_AssignedTo
{
    protected $_label = 'Assigned to user';
    protected $_name = 'assigned_to';

    /**
     * @param string label the use on the element object
     * @return fluent interface
     */
    public function setLabel($label = '')
    { 
        if (isset($label) and is_string($label)) {
            $this->_label = $label;
        } 
        return $this;
    }
    
    /**
     * @param string name the element name 
     * @return fluent interface
     */
    public function setName($name = '')
    {
        if (isset($name) and is_string($name)) {
            $this->_name = $name;
        } 

        return $this;
    }

    /**
     * @return object Zend_Dojo_Form_Element_Combobox
     */
    public function getElement()
    {
        $user = new Core_Model_User;
        $userData = $user->fetch();
        
        $element = new Zend_Dojo_Form_Element_FilteringSelect($this->_name);
        $element->setLabel($this->_label)
                ->setAutoComplete(true)
                ->setStoreId('userStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/user/jsonstore'))
                ->setAttrib("searchAttr", "email")
                ->setAttrib('displayedValue', $userData->email)
                ->setRequired(false);
       return $element;
    }
}


