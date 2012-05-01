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

class Core_Model_Opportunity_Report
{
    public $db;

    public function __construct($opportunityId = null)
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
        $opportunityModel = new Core_Model_Opportunity;
        $select = $opportunityModel->getfetchAllSelectObject();
        
        /*
          * Filter the user input date
          */
         if ($input['created_from'] != '') {
            $dateFrom = new Zend_Date($input['created_from'], 'yyyy.MM.dd');
            $createdFrom = $dateFrom->getTimestamp();
            $select->where("o.created > ?",$createdFrom);
         } 
         
         if($input['created_to'] != '') {
            $dateTo = new Zend_Date($input['created_to'], 'yyyy.MM.dd');
            $createdTo = $dateTo->getTimestamp();
            $select->where("o.created < ?",$createdTo);
         }
       
         if ($input['expected_close_date_from'] != '') {
            $dateFrom = new Zend_Date($input['expected_close_date_from'], 'yyyy.MM.dd');
            $createdFrom = $dateFrom->getTimestamp();
            $select->where("expected_close_date > ?",$createdFrom);
         } 
         
         if($input['expected_close_date_to'] != '') {
            $dateTo = new Zend_Date($input['expected_close_date_to'], 'yyyy.MM.dd');
            $createdTo = $dateTo->getTimestamp();
            $select->where("expected_close_date < ?",$createdTo);
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
                        $expression .= " or o.branch_id = $value";  
                    } else {
                        $expression .= "  o.branch_id = $value";  
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
            $assignedTo = User::getUserIdFromEmail($input['assigned_to']);
        }
       
        $assignedTo = $input['assigned_to']; 
        if (is_numeric($assignedTo)) {
            $select->where('l.assigned_to = ?', $assignedTo);
        }
        return $select;         
    }
}
