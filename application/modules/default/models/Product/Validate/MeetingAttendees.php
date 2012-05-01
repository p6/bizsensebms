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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Product_Validate_MeetingAttendees extends Zend_Validate_Abstract
{

    const ITEM = 'item';    

    /**
     * @see Zend_Validate_Abstract::_messageTemplates
     */
    protected $_messageTemplates = array(
        self::ITEM => "'%value%' is left blank"
    );
    
    /**
     * The array containing all the error messages 
     */ 
    protected $_allItemsMessages  = array();

    /**
     * Whether or not the validation from the last isValid call failed
     */
    protected $_validationStatus = true;

    /**
     * Array after the submmited rowset is filtered to retain sequence
     */
    protected $_filteredContactItemsValue = array();
    protected $_filteredUserItemsValue = array();
    protected $_filteredLeadItemsValue = array();


    /**
     * @see Zend_Validate_Abstract::isValid()
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        $this->filterContactRemovedRows();
        $this->filterUserRemovedRows();
        $this->filterLeadRemovedRows();
        $this->validateContactItemsRowset();
        $this->validateUserItemsRowset();
        $this->validateLeadItemsRowset();
        return $this->_validationStatus;

    }

    /**
     * @return void
     */
    public function filterContactRemovedRows()
    {
        $filtered = array();

        if ( (!is_array($this->_value))) {
            return;
        } 

        if (!isset($this->_value['contact_id'])) {
            return;
        }

        $contactItemsSubmitted = $this->_value['contact_id'];
       
        $totalContactRowsSubmitted = count($contactItemsSubmitted);
    
        foreach ($contactItemsSubmitted as $key=>$value){
            $filteredContact[] = array(
                'contact_id'    =>  $value, 
            );
        }

        $this->_filteredContactItemsValue = $filteredContact;
    }

    public function filterUserRemovedRows()
    {
        $filtered = array();

        if ( (!is_array($this->_value))) {
            return;
        } 

        if (!isset($this->_value['user_id'])) {
            return;
        }
        $userItemsSubmitted = $this->_value['user_id'];
        $totalUserRowsSubmitted = count($userItemsSubmitted);
    
        foreach ($userItemsSubmitted as $key=>$value){
            $filteredUser[] = array(
                'user_id'    =>  $value, 
            );
        }
        $this->_filteredUserItemsValue = $filteredUser;    
    }


    public function filterLeadRemovedRows()
    {
        $filtered = array();

        if ( (!is_array($this->_value))) {
            return;
        } 

        if (!isset($this->_value['lead_id'])) {
            return;
        }
        $leadItemsSubmitted = $this->_value['lead_id'];
        $totalLeadRowsSubmitted = count($leadItemsSubmitted);
    
        foreach ($leadItemsSubmitted as $key=>$value){
            $filteredLead[] = array(
                'lead_id'    =>  $value, 
            );
        }
        $this->_filteredLeadItemsValue = $filteredLead;    
    }

    /**
     * @return bool
     */
    public function validateContactItemsRowset()
    {
        $isValid = true;
        $uniqueFilteredItemsValue = array();

        /**
         * If the user removes a row of items randomnly, the array sequence will be lost
         * We make sure we work on only those rows that are actually submitted
         */
        foreach ($this->_filteredContactItemsValue as $key=>$value) {
            if (!is_numeric($value['contact_id'])) {
                $isValid = false;
                $this->_allItemsMessages[] = "Name for contact item " .  ($key+1) . " is invalid";
            }
            
            if (!(in_array($value['contact_id'], $uniqueFilteredItemsValue))) {
                $uniqueFilteredItemsValue[] = $value['contact_id'];
            }
            else {
                $isValid = false;
                $this->_allItemsMessages[] = "Name of contact item " .  ($key+1) . " already exists";
            }
        }
        $this->_validationStatus = $isValid;    
        return $isValid;
    }
    
    public function validateUserItemsRowset()
    {
        $isValid = true;
        $uniqueFilteredItemsValue = array();

        foreach ($this->_filteredUserItemsValue as $key=>$value) {
            if (!is_numeric($value['user_id'])) {
                $isValid = false;
                $this->_allItemsMessages[] = "Name for user item " .  ($key+1) . " is invalid";
            }
            
            if (!(in_array($value['user_id'], $uniqueFilteredItemsValue))) {
                $uniqueFilteredItemsValue[] = $value['user_id'];
            }
            else {
                $isValid = false;
                $this->_allItemsMessages[] = "Name of user item " .  ($key+1) . " already exists";
            }
        }
        $this->_validationStatus = $isValid;    
        return $isValid;
    }


    public function validateLeadItemsRowset()
    {
        $isValid = true;
        $uniqueFilteredItemsValue = array();

        foreach ($this->_filteredLeadItemsValue as $key=>$value) {
            if (!is_numeric($value['lead_id'])) {
                $isValid = false;
                $this->_allItemsMessages[] = "Name for lead item " .  ($key+1) . " is invalid";
            }
            
            if (!(in_array($value['lead_id'], $uniqueFilteredItemsValue))) {
                $uniqueFilteredItemsValue[] = $value['lead_id'];
            }
            else {
                $isValid = false;
                $this->_allItemsMessages[] = "Name of lead item " .  ($key+1) . " already exists";
            }
        }
        $this->_validationStatus = $isValid;    
        return $isValid;
    }

    /**
     * @return array the error messages
     */
    public function getAllItemsMessages()
    {
        return $this->_allItemsMessages;
    }

    /**
     * @return array filtered items
     */
    public function getContactFilteredItems()
    {
        return $this->_filteredContactItemsValue;
    }

    public function getUserFilteredItems()
    {
        return $this->_filteredUserItemsValue;
    }

    public function getLeadFilteredItems()
    {
        return $this->_filteredLeadItemsValue;
    }

    /**
     * @return array of filtered products
     */
    public function getContactFilteredJSON()
    {
       $this->_filteredContactJSON =  json_encode($this->_filteredContactItemsValue); 
       return $this->_filteredContactJSON;
    }

    public function getUserFilteredJSON()
    {
       $this->_filteredUserJSON =  json_encode($this->_filteredUserItemsValue); 
       return $this->_filteredUserJSON;
    }

    public function getLeadFilteredJSON()
    {
       $this->_filteredLeadJSON =  json_encode($this->_filteredLeadItemsValue); 
       return $this->_filteredLeadJSON;
    }

}

