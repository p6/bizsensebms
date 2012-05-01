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
class LeadController extends Zend_Controller_Action
{

    /*
     * Lead model
     */
    protected $_model;

    /**
     * @var object core model save
     */
    protected $_saveData;

    /*
     * Initialize operations, model and acl
     */
    function init()
    {
        $this->_model = new Core_Model_Lead;
    }

    /**
     * Browsable, sortable, searchable paginated List of leads
     */
    public function indexAction() 
    {
        $form = new Core_Form_Lead_Search;
        $form->populate($_GET);
        $this->view->form = $form;    
        
        $sort = $this->_getParam('sort');
        $paginator = $this->_model->getPaginator($form->getValues(), $sort);

        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

        /**
         * Notify the view if the submit was hit on the search form
         */
        if ($form->getValue('submit') == 'Search') {
            $this->view->wasSearched = true;
        }

    } 

    /**
     * Create a new lead
     *
     * @see Zend_Controller_Action
     */  
    public function createAction() 
    {

        $form = new Core_Form_Lead_Create;

        $action = $this->_helper->url(
            'create', 
            'lead', 
            'default'
        );
        $form->setAction($action);

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $leadId = $this->_model->create($form->getValues());
                $status = $this->_model->getStatus();
                $this->_helper->FlashMessenger("Lead added successfully");
                $this->_helper->redirector(
                    'viewdetails', 
                    'lead', 
                    'default', 
                    array('lead_id'=>$leadId)
                );

            } else {
                $form->populate($_POST);

                $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
        }

    } 

    /**
     * Import leads from a CSV file
     */
    public function importAction()
    {
        $form = new Core_Form_Lead_Import;

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->lead_import_csv->receive()) {
                    $location = $form->lead_import_csv->getFileName();
                }
                $noOfAffectedRows = $this->_model->import($form->getValues(), $location);
                if (is_string($noOfAffectedRows)){
                    $this->_helper->FlashMessenger("Error in importing list");
                }elseif($noOfAffectedRows >= 1 ) {
                    $this->_helper->FlashMessenger("
                        $noOfAffectedRows Leads imported successfully");
                }
                $this->_helper->redirector(
                    'index',
                    'lead',
                    'default'
                );
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
        }
 
    }

    /**
     * Edit a lead
     */
    public function editAction() 
    {
        $leadId = $this->_getParam('lead_id');
        $this->view->leadId = $leadId;	

        $form = new Core_Form_Lead_Edit($leadId);
        $action = $this->_helper->url(
            'edit', 
            'lead', 
            'default',
            array(
                'lead_id' => $leadId
            )
        );
        $form->setAction($action);

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $lead = new Core_Model_Lead($leadId);
                $lead->edit($form->getValues());
                $this->_helper->FlashMessenger('Lead edited successfully');
                $this->_helper->redirector(
                    'viewdetails',
                    'lead',
                    'default',
                    array(
                        'lead_id' => $leadId
                    )
                );
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }
        } else {
            $this->view->form = $form;
        }
    } 

    /**
     * View the details of a lead
     */
    public function viewdetailsAction() 
    {
        $leadId = $this->_request->getParam('lead_id');
        $lead = new Core_Model_Lead($leadId);
        $this->view->lead = $lead->fetch();
        $this->view->lead_id = $leadId;	
    } 


    /**
     * Delete the lead
     */
    public function deleteAction()
    {       
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setLeadId($this->_getParam('lead_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The lead was successfully deleted'; 
        } else {
           $message = 'The lead could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'lead', 'default');
    }

    /**
     * Assign lead to a branch while creating and editing a lead
     * @output Zend_Dojo_Data for the autocomplete field
     */
    public function assigntobranchAction()
    {

        $data = $this->_model->getAssignedToBranchDojoData();
        $this->_helper->autoCompleteDojo($data);
    }



    /*
     * Convert a lead to a contact, account and opportunity
     */
    public function convertAction() 
    {
        $leadId = $this->_getParam('lead_id');
        $this->view->leadId = $leadId;
        $this->_model->setLeadId($leadId);

        $form = new Core_Form_Lead_Convert($leadId);
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {

                $lead = new Core_Model_Lead($leadId);
                $ids = $lead->convert($form->getValues()); 
                $this->view->message = "Lead converted successfully";
                $this->view->contactId = $ids['contactId'];
                $this->view->leadId = $leadId;
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
        }

    }

    /*
     * Menu links to CRUD operations of lead source and lead status
     */
    public function settingsAction() 
    {
        
    }

    /**
     * Set the default lead assignee
     */
    public function setdefaultassigneeAction()
    {
        $form = new Core_Form_Lead_SetDefaultAssignee();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->setDefaultAssigneeId(
                    $form->getValue('assigned_to')
                );
                if ($form->getValue('set_unassigned') == 1) {
                    $this->_model->assignUnassignedLeadsTo(
                        $form->getValue('assigned_to')
                    );
                }
                $this->view->message = 'Settings successfully saved';
            } else {
                $form->populate($this->getRequest()->getPost());
            } 
        } else {
            $form->populate(
                array('assigned_to' => $this->_model->getDefaultAssigneeId())
            );
        }

    }

    /**
     * Browsable, sortable, searchable list of lead notes
     */
    public function notesAction()
    {
        $leadId = $this->_getParam('lead_id');
        $this->_model->setLeadId($leadId);
        $this->view->leadId = $leadId;
        $notes =  $this->_model->getNotes();
        $paginator = $notes->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

    }

    /**
     * Create a note to this lead
     */
    public function createnoteAction()
    {
        $leadId = $this->_getParam('lead_id');
        $this->view->leadId = $leadId;
        $this->_model->setLeadId($leadId);
        $model = $this->_model->getNotes();
        $form = new Core_Form_Account_Note_Create();
        $form->setAction($this->view->url(array(
                'module' => 'default',
                'controller' => 'lead',
                'action' => 'createnote',
                'lead_id' => $leadId,
            ), NULL, TRUE));

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $model->create($form->getValues());
                $this->_helper->FlashMessenger('Lead note was created successfully');
                $url = $this->view->url(array(
                    'module' => 'default',
                    'controller' => 'lead',
                    'action' => 'notes',
                    'lead_id' => $leadId,        
                ), null, true);
	 	        $this->_redirect($url);
		        $this->view->message = "Lead note was created successfully";
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

         } else {
            $this->view->form = $form;
	     }

    }

    /**
     * JSON store of all leads
     */
    public function allcontactjsonstoreAction()
    {

        $this->_helper->layout->disableLayout();
        $rowset = $this->_model->fetchAll();
        $leadData = array();
        foreach ($rowset as $row) {
            $leadData[] = $row->toArray();
        }
        $data = new Zend_Dojo_Data('lead_id', (array) $leadData);
        $data->setLabel('leads');
        $this->_helper->autoCompleteDojo($data);

    }



    /**
     * @development use the Contact_Data model and Zend_Dojo_Data components to generate the list
     * Also apply ACL to the list
     */
    public function jsonstoreAction()
    {
        $db = Zend_Registry::get('db');

        $this->_helper->layout->disableLayout();

        $sql = "SELECT CONCAT_WS(' ', first_name, middle_name, last_name) as 
            lead, lead_id as id FROM lead";
        $result = $db->fetchAll($sql);

        $this->view->result = $result;
    }

    public function viewrelatedcallsAction()
    {
        $leadId = $this->_getParam('lead_id');
        $calls = new Core_Model_Activity_Call;
        $callsList = $calls->fetchLeads($leadId);
        $this->view->callsList = $callsList;
    }

    public function viewrelatedmeetingsAction()
    {
        $leadId = $this->_getParam('lead_id');
        $meeting = new Core_Model_Activity_Meeting;
        $meetingList = $meeting->fetchLeads($leadId);
        $this->view->meetingList = $meetingList;
    }

 
} 
