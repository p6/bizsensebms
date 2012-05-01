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
class ContactController extends Zend_Controller_Action 
{
    protected $_data;
    protected $_model;
    
    public function init()
    {
        $this->_model = new Core_Model_Contact;
    }

    /**
     * Browsable, sortable, searchable list of contacts
     */
    public function indexAction() 
    {
        $sForm = new Core_Form_Contact_Search;
        $form = $sForm->getForm();
        $form->populate($_GET);
        $this->view->form = $form;

	    $sort = $this->_getParam('sort');
        $paginator = $this->_model->getPaginator($form->getValues(), $sort);
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
        
        /*
         * Notify the view if the submit was hit on the search form
         */   
        if ($form->getValue('submit') == 'Search') {
            $this->view->wasSearched = true;        
        }
    } 

    /**
     * View the details of the contact
     */
    public function viewdetailsAction() 
    {
	    $contactId = $this->_request->getParam('contact_id');
        $contact = new Core_Model_Contact($contactId);
	    $this->view->contact = $contact->fetch();
    } 

    /**
     * Create a contact
     */
    public function createAction() 
    {
        $form = new Core_Form_Contact_Create;
        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
                $contact = new Core_Model_Contact();
                $contactId = $contact->create($form->getValues());
                $this->_helper->FlashMessenger('Contact created successfully');
                $this->_redirect($this->view->url(array(
                    'module'        =>  'default',
                    'controller'    =>  'contact',
                    'action'        =>  'viewdetails',
                    'contact_id'    =>  $contactId,
                )));
            	$this->view->message = "Contact created successfully";
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
	    }

    } 

    /**
     * Edit a contact
     */
    public function editAction() 
    {
	    $contactId = $this->_getParam('contact_id');	
        $contact = new Core_Model_Contact($contactId);
         
        $form = new Core_Form_Contact_Edit($contactId);
        $form->setAction($this->view->url(array(
            'module'        =>  'default',
            'controller'    =>  'contact',
            'action'        =>  'edit',
            'contact_id'    =>  $contactId,
        ), null, true));
        if ($this->_request->isPost()){
            if ($form->isValid($_POST)) {
                $data = $this->_data;
                $contact->edit($form->getValues());
		        $this->_helper->FlashMessenger('Contact edited successfully');
                $this->_redirect($this->view->url(array(
                    'module'        =>  'default',
                    'controller'    =>  'contact',
                    'action'        =>  'viewdetails',
                    'contact_id'    =>  $contactId,
                )));
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
           }

        } else {
            $this->view->form = $form;
	    }
	
    } 

    /**
     * Delete a contact
     */
    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setContactId($this->_getParam('contact_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The contact was successfully deleted'; 
        } else {
           $message = 'The contact could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'contact', 'default');
    }

    /**
     * import contacts
     */
    public function importAction()
    {
        $form = new Core_Form_Contact_Import; 
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->contact_import_csv->receive()) {
                    $location = $form->contact_import_csv->getFileName();
                }
            $noOfAffectedRows = $this->_model->import($form->getValues(), $location);
            if($noOfAffectedRows >= 1 ) {
                $this->_helper->FlashMessenger("
                $noOfAffectedRows contacts imported successfully");
            }
            else {
                $this->_helper->FlashMessenger("Error in importing contact");
            }
            $this->_helper->redirector('index', 'contact', 'default');
            }else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

        } else {
            $this->view->form = $form;
        }
    }

    /**
     * @development use the Contact_Data model and Zend_Dojo_Data components to generate the list
     * Also apply ACL to the list
     */
    public function jsonstoreAction()
    {
        $this->_helper->layout->disableLayout();
              
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('contact_id', $items);
        $this->_helper->AutoCompleteDojo($data);
    }

    /**
     * Assign contact to a branch while creating and editing a lead
     * @output Zend_Dojo_Data for the autocomplete field
     */
    public function assigntobranchAction()
    {

        $data = $this->_model->getAssignedToBranchDojoData();
        $this->_helper->autoCompleteDojo($data);
    }

    /**
     * JSON store of all contacts
     */
    public function allcontactjsonstoreAction()
    {
        $this->_helper->layout->disableLayout();
        $rowset = $this->_model->fetchAll();
        $contactData = array();
        foreach ($rowset as $row) {
            $contactData[] = $row->toArray();
        }
        $data = new Zend_Dojo_Data('contact_id', (array) $contactData);
        $data->setLabel('contacts');
        $this->_helper->autoCompleteDojo($data);

    }

    /**
     * Self service account for the contact
     */
    public function ssaccountAction()
    {
        $contactId = $this->_getParam('contact_id');
        $contactModel = $this->_model->setId($contactId);
        $this->view->selfServiceAccountEnabled = 
            $contactModel->getSelfServiceAccountStatus();
        $this->view->contactId = $contactId;

        if ($this->_request->isPost()){
            
            if ($_POST['disable']) {
               $contactModel->disableSelfService();
               $this->view->message = "Self service disabled for the contact";
               $this->_helper
                    ->FlashMessenger(
                    "Self service disabled for the contact"
               ); 
            }
            else {       
                $contactModel->enableSelfService();
                $this->view->message = "Self service enabled for the contact";
                $this->_helper
                    ->FlashMessenger(
                    "Self service enabled for the contact"
                  );
                $this->_helper
                    ->_redirector(
                        'ssaccount', 'contact', 'default', 
                        array('contact_id' => $contactId)
                    );
            }
        }

    }
    
    /**
     * Browsable, sortable, searchable list of contact notes
     */
    public function notesAction()
    {
        $contactId = $this->_getParam('contact_id');
        $this->_model->setContactId($contactId);
        $this->view->contactId = $contactId;
        $notes =  $this->_model->getNotes();
        $paginator = $notes->getPaginator();
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

    }

    /**
     * Create a note to this contact
     */
    public function createnoteAction()
    {
        $contactId = $this->_getParam('contact_id');
        $this->view->contactId = $contactId;
        $this->_model->setContactId($contactId);
        $model = $this->_model->getNotes();
        $form = new Core_Form_Account_Note_Create();
        $form->setAction($this->view->url(array(
                'module' => 'default',
                'controller' => 'contact',
                'action' => 'createnote',
                'contact_id' => $contactId,
            ), NULL, TRUE));

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $model->create($form->getValues());
                $this->_helper->FlashMessenger('Contact note was created successfully');
                $url = $this->view->url(array(
                    'module' => 'default',
                    'controller' => 'contact',
                    'action' => 'notes',
                    'contact_id' => $contactId,        
                ), null, true);
	 	        $this->_redirect($url);
		        $this->view->message = "Contact note was created successfully";
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

         } else {
            $this->view->form = $form;
	     }

    }
    
    public function initializeledgerAction()
    {
        $contactId = $this->_getParam('contact_id'); 
        $this->_model->setContactId($contactId);
  
        $form = new Core_Form_Finance_Ledger_InitializeLedger;
        $form->setAction($this->_helper->url(
                'initializeledger', 
                'contact', 
                'default',
                array(
                    'contact_id'=>$contactId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->initializeLedger($form->getValues());
                $this->_helper->FlashMessenger('The Ledger Initialized successfully');
                $this->_helper->redirector('viewdetails', 'contact', 'default',
                    array('contact_id'=>$contactId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }

    public function viewrelatedcallsAction()
    {
        $contactId = $this->_getParam('contact_id');
        $calls = new Core_Model_Activity_Call;
        $callsList = $calls->fetchContacts($contactId);
        $this->view->callsList = $callsList;
    }

    public function viewrelatedmeetingsAction()
    {
        $contactId = $this->_getParam('contact_id');
        $meeting = new Core_Model_Activity_Meeting;
        $meetingList = $meeting->fetchContacts($contactId);
        $this->view->meetingList = $meetingList;
    }
    
    /**
     * Subscribe for mailing list
     */
    public function subscribetolistAction()
    {
        
    }

} 
