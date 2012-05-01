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

class Core_Model_Opportunity_Index extends Core_Model_Index_Abstract
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
        $table = $this->_model->getTable();
        $select = $table->select();
        $select->setIntegrityCheck(false);
        $search = $this->_search;
        $sort = $this->_sort;
        $select->from(array('o' => 'opportunity'),
                  array('opportunity_id', 'name', 'amount', 'expected_close_date', 'account_id'))
                ->joinLeft(array('a'=>'account'), 
                    'a.account_id = o.account_id', array('a.account_name'))
                ->joinLeft(array('b'=>'branch'), 
                    'b.branch_id = o.branch_id', array('b.branch_name'))
                ->joinLeft(array('u'=>'user'), 
                    'u.user_id = o.assigned_to', array('u.email as assignedToUser'));

        /*
         * Sort data    
         */
        switch ($sort) {

            case "nameAsc" :
                $select->order('name');
            break;
            case "nameDes" :
                $select->order('name DESC');
            break;

            case "amountAsc" :
                $select->order('amount');
            break;
            case "amountDes" :
                $select->order('amount DESC');
            break;

            case "expectedCloseDateAsc" :
                $select->order('expected_close_dte');
            break;
            case "expectedCloseDateDes" :
                $select->order('expected_close_date DESC');
            break;

            case 'accountAsc' :
                $select->order('a.account_name');
            break;
            case 'accountDes' :
                $select->order('a.account_name DESC');
            break;

            case 'userAsc' :
                $select->order('u.email');
            break;
            case 'userDes' :
                $select->order('u.email DESC');
            break;

            case 'branchAsc' :
                $select->order('b.branch_name');
            break;
            case 'branchDes' :
                $select->order('b.branch_name DESC');
            break;

            default:
                $select->order('o.created DESC');
        }

        /*
         * Search 
         */

        $name = $search['name'];
        $select->where('o.name like ?', '%' . $name . '%');

        $accountId = $search['account_id'];
        $select->where('o.account_id like ?', '%' . $accountId . '%');

        $leadSourceId = $search['lead_source'];
		if(is_array($leadSourceId)){
			$lastElement = end($leadSourceId);
			reset($leadSourceId);
			$leadSourceIdSyn = "o.lead_source_id =";
			$multipleLeadSource = "";
			foreach($leadSourceId as $key=>$value){
            	$multipleLeadSource = $multipleLeadSource . $leadSourceIdSyn. $value; 
            	if($value != $lastElement){
            		$multipleLeadSource = $multipleLeadSource . " OR ";
            	}
        	}
        $select->where($multipleLeadSource);
		}

		$salesStageId = $search['sales_stage_id'];
        if(is_array($salesStageId)){
            $lastElement = end($salesStageId);
            reset($salesStageId);
            $salesStageIdSyn = "o.sales_stage_id =";
            $multipleSalesStage = "";
            foreach($salesStageId as $key=>$value){
                $multipleSalesStage = $multipleSalesStage . $salesStageIdSyn. $value;
                if($value != $lastElement){
                    $multipleSalesStage = $multipleSalesStage . " OR ";
                }
            }
        $select->where($multipleSalesStage);
        }

        $assignedTo = $search['assigned_to'];
        if ($assignedTo) {
             $select->where('o.assigned_to = ?', $assignedTo);
        }

        $branchId = $search['branch_id'];
        if (is_numeric($branchId)) {
            $select->where('o.branch_id = ?', $branchId);
        }
        
        /**
         * Apply access controls to the select object
         */
        if ($acl->isAllowed($user, 'view all opportunities')) {
            $select->where('o.assigned_to like ?', '%' . '' . '%');
        } else if ($acl->isAllowed($user, 'view own branch opportunities')) {
            $select->where('o.branch_id = ?', $user->getBranchId());
        } else {
            $select->where('o.assigned_to = ?', $user->getUserId());
        }
        $select->orWhere("o.assigned_to = ''");
        
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($select));
        return $paginator;
    }
}
