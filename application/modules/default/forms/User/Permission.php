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

class Core_Form_User_Permission extends Zend_Form
{

    public function __construct($roleId)
    {
        $this->privilegeSubForm($roleId);
        $this->addElement('submit', 'submit', array 
            (
                'class' => 'submit_button'
            ));
        parent::__construct();
    }   

    public function privilegeSubForm($roleId = null)
    {
                
        $subForm = new Zend_Form_SubForm();
        $privilegeModel = new Core_Model_Privilege;
        $privileges = $privilegeModel->fetchAll();
        $accessModel = new Core_Model_Access;
        $access = $accessModel->fetchAllByRoleId($roleId);
        
        /**
         * Store the privilege_id already set in access table in myArray
         * Use $myArray to populate the form elements
         */
        $myArray = array();
        foreach ($access as $key=>$value) {
            $myArray[] = $value['privilege_id'];
        }
        
        foreach($privileges as $privilege) {
            $row = $privilege;
            $subElement = $subForm->createElement('checkbox', $row['privilege_id'])
                                ->setLabel($row['name']);
            
            if (in_array($row['privilege_id'], $myArray)) {
                $subElement->setChecked(true);
            } 
            $subForm->addElement($subElement);
        
        }


        $this->addSubForm($subForm, 'privilege');

    }

        
}
