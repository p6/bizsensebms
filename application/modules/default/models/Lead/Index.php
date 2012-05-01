<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 */

class Core_Model_Lead_Index extends Core_Model_Index_Abstract
{

    /*
     * Process search form and sort values 
     * @return Zend_Db_Select object
     * Which can be passed to Zend_Paginator
     */
    public function getPaginator()
    {
        $acl = Zend_Registry::get('acl');
        $user = Zend_Registry::get('user');
        $db = Zend_Registry::get('db');

        $sort = $this->_sort;
        $search = $this->_search;

        $select = $db->select();
        $select->from(array('l' => 'lead'),
                  array('lead_id', 'first_name', 'last_name', 'company_name', 'mobile'))
            ->joinLeft(array('ls'=>'lead_source'),
                'l.lead_source_id = ls.lead_source_id', array('ls.name'=>'name as lead_source_name'))
            ->joinLeft(array('lst'=>'lead_status'),
                'l.lead_status_id = lst.lead_status_id', array('lst.name'=>'name as lead_status_name'));
        /*
         * Show only leads that are not converted by default
         */
        $select->where('l.converted = ?' , 0);


        /* 
         * Sort data    
         */

        switch ($sort) {

            case "firstNameAsc" :
                $select->order('first_name');
            break;
            case "firstNameDes" :
                $select->order('first_name DESC');
            break;
            case "lastNameAsc" :
                $select->order('last_name');
            break;
            case "lastNameDes" :
                $select->order('last_name DESC');
            break;
            case "companyNameAsc" :
                $select->order('company_name');
            break;
            case "companyNameDes" :
                $select->order('company_name DESC');
            break;
            case "emailAsc" :
                $select->order('email');
            break;
            case "emailDes" :
                $select->order('email DESC');
            break;
            case "mobileAsc" :
                $select->order('mobile');
            break;
            case "mobileDes" :
                $select->order('mobile DESC');
            break;

            case "leadSourceAsc" :
                $select->order('ls.name');
            break;
            case "leadSourceDes":
                $select->order('ls.name DESC');
            break;

            case 'leadStatusAsc' :
                $select->order('lst.name');
            break;
            case "leadStatusDes":
                $select->order('lst.name DESC');
            break;
            
            default:
                $select->order('l.created DESC');

        }

        /**
         * Search 
         */
        $name = $search['name'];
        $name = $db->quote("%$name%");
        $select->where("l.first_name like $name or l.middle_name like $name or l.last_name like $name");

        $companyName = $search['companyName'];
        $select->where('l.company_name like ?', '%' . $companyName . '%');
        
		$leadSourceId = $search['lead_source_id'];
        if(is_array($leadSourceId)){
            $lastElement = end($leadSourceId);
            reset($leadSourceId);
            $leadSourceIdSyn = "l.lead_source_id =";
            $multipleLeadSource = "";
            foreach($leadSourceId as $key=>$value){
                $multipleLeadSource = $multipleLeadSource . $leadSourceIdSyn. $value;
                if($value != $lastElement){
                    $multipleLeadSource = $multipleLeadSource . " OR ";
                }
        	}
        	$select->where($multipleLeadSource);
		}

        $leadStatusId = $search['lead_status_id'];
        if(is_array($leadStatusId)){
            $lastElement = end($leadStatusId);
            reset($leadStatusId);
            $leadStatusIdSyn = "l.lead_status_id =";
            $multipleLeadStatus = "";
            foreach($leadStatusId as $key=>$value){
                $multipleLeadStatus = $multipleLeadStatus . $leadStatusIdSyn. $value;
                if($value != $lastElement){
                    $multipleLeadStatus = $multipleLeadStatus . " OR ";
                }
            }
        $select->where($multipleLeadStatus);
        }

        if (isset($search['assigned_to'])) {
            $assignedTo = $search['assigned_to'];
        } else {
            $assignedTo = null;
        }
        if (is_numeric($assignedTo)) {
            $select->where('l.assigned_to = ?', $assignedTo);
        }

        if (isset($search['branchId'])) {
            $branchId = $search['branchId'];
        } else {
            $branchId = null;
        }    
        if (is_numeric($branchId)) {
            $select->where('l.branch_id = ?',$branch_id);
        }
   
        
        if (isset($search['city'])) {
            $city = $search['city'];
            $select->where('l.city like ?', '%' . $city . '%');
        }    
 
        /**
         * Apply access controls to the select object
         */
        if ($acl->isAllowed($user, 'view all leads')) {
            $select->where('l.assigned_to like ?', '%' . '' . '%');
        } else if ($acl->isAllowed($user, 'view own branch leads')) {
            $select->where('l.branch_id like ?', '%' . $user->getBranchId() . '%');
        } else {
            $select->where('l.assigned_to like ?', '%' . $user->getUserId() . '%');
        }
        $select->orWhere("l.assigned_to = ''");

        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($select));
        return $paginator; 
    }
 
}
