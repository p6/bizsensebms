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

class Core_Model_Contact_Report
{
    public $db;

    public function __construct($contactId = null)
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
        $contactModel = new Core_Model_Contact;
        $select = $contactModel->getIndexSelectObject();

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
                        $expression .= " or c.branch_id = $value";  
                    } else {
                        $expression .= "  c.branch_id = $value";  
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
       
        $assignedTo = $input['assigned_to']; 
        if (is_numeric($assignedTo)) {
            $select->where('l.assigned_to = ?', $assignedTo);
        }
        return $select;         
    }
}
