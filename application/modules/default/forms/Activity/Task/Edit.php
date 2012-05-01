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

class Core_Form_Activity_Task_Edit extends Core_Form_Activity_Task_Create
{
    protected $_taskModel;

    public function __construct($taskModel)
    {
            $this->_taskModel = $taskModel;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $formValues = $this->_taskModel->fetch();
        $startTime = new Zend_Date($formValues['start_date']);
        $endTime = new Zend_Date($formValues['end_date']);
        $formValues['start_time'] = 'T' . $startTime->get(Zend_Date::HOUR) 
                                . ':' . $startTime->get(Zend_Date::MINUTE) . ':' 
                                . $startTime->get(Zend_Date::SECOND);
        $formValues['end_time'] = 'T' . $endTime->get(Zend_Date::HOUR) 
                                . ':' . $endTime->get(Zend_Date::MINUTE) . ':' 
                                    . $endTime->get(Zend_Date::SECOND);

        Zend_Date::setOptions(array('format_type' => 'php'));
        $startDate = new Zend_Date($formValues['start_date']);
        $formValues['start_date'] = $startDate->toString('Y-m-d');
        $endDate = new Zend_Date($formValues['end_date']);
        $formValues['end_date'] = $endDate->toString('Y-m-d');

        $this->populate($formValues);
    }    
}
