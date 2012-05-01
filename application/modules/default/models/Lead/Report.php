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

class Core_Model_Lead_Report
{
    public $db;

    public function __construct($leadId = null)
    {
        $this->db = Zend_Registry::get('db');
    }

    /*
     * Generates Zend_Db_Select object from the input
     * @param array of user input
     */
    public function generateSelectObject($input = array())
    {
        /* 
         * Construct the select object
         */
        $select = $this->db->select();
        $select->from(array('l' => 'lead'),
                  array('lead_id', 'first_name', 'last_name', 'company_name', 'created', 'updated'))
               ->joinLeft(array('ls'=>'lead_source'),
                            'l.lead_source_id = ls.lead_source_id', array('ls.name'=>'name'))
               ->joinLeft(array('lst'=>'lead_status'),
                            'l.lead_status_id = lst.lead_status_id', array('lst.name'=>'name'))
               ->joinLeft(array('p'=>'profile'),
                            'p.user_id = l.assigned_to', array('p.first_name as asssignedToFirstName'))
               ->joinLeft(array('b'=>'branch'),
                            'b.branch_id = l.branch_id', array('b.branch_name'));

         
         if (!$input) {
            return $select;         
         }
         /*
          * Filter the user input date
          */
         if ($input['createdFrom'] != '') {
            $dateFrom = new Zend_Date($input['createdFrom'], 'yyyy.MM.dd');
            $createdFrom = $dateFrom->getTimestamp();
            
            $select->where("created > ?",$createdFrom);
         } 
         
         if($input['createdTo'] != '') {
            $dateTo = new Zend_Date($input['createdTo'], 'yyyy.MM.dd');
            $createdTo = $dateTo->getTimestamp();
            $select->where("created < ?",$createdTo);
         }
       
         if ($input['lastUpdatedFrom'] != '') {
            $dateFrom = new Zend_Date($input['lastUpdatedFrom'], 'yyyy.MM.dd');
            $createdFrom = $dateFrom->getTimestamp();
            $select->where("lastUpdated > ?",$createdFrom);
         } 
         
         if($input['lastUpdatedTo'] != '') {
            $dateTo = new Zend_Date($input['lastUpdatedTo'], 'yyyy.MM.dd');
            $createdTo = $dateTo->getTimestamp();
            $select->where("lastUpdated < ?",$createdTo);
         }
         
        /*
         * Add where clauses using the converted input element
         */        
        $converted = $input['converted'];
        if (sizeof($converted) > 0)  {
            /*
             * Both converted and not converted
             */
            if ( ( in_array('converted', $converted) ) and ( in_array('notConverted', $converted) )) {
                $select->where("l.converted = 1 or l.converted = 0"); 
            } else 
            
            /*
             * Converted only
             */
            if (in_array('converted', $converted)) {
                $select->where('l.converted like ?', '%' . '1' . '%'); 
            } else 
            
            /*
             * Not converted only
             */
            if (in_array('notConverted', $converted)) {
               $select->where('l.converted like ?', '%' . '0' . '%'); 
            }
        }   

        /*
         * Add where clauses using branchId input element 
         */
        $branchId = $input['branch_id'];   
        $expression = '';
        $count = 0;
        if ($branchId) {
            foreach ($branchId as $value){
                if (is_numeric($value)) {
                    $count++;
                    if ($count > 1) {
                        $expression .= " or l.branch_id = $value";  
                    } else {
                        $expression .= "  l.branch_id = $value";  
                     }
                } 
            }
        }
        if ($count > 0) {
            $select->where($expression);
        }    

        /*
         * Add where clauses using userId input element
         */
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($input['assigned_to'])) {
            $assignedTo = Core_Model_User::getUserIdFromEmail($input['assigned_to']);
        }
        
        if (isset($assignedTo) && (is_numeric($assignedTo)) ) {
            $select->where('l.assigned_to = ?', $assignedTo);
        }
        return $select;         
    }
}
