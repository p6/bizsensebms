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

/* @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class CampaignController extends Zend_Controller_Action 
{
    /**
     * @var object Core_Model_Campaign
     */
    protected $_model;

    /**
     * Initialize the controller
     */
    public function init()
    {
        $this->_model = new Core_Model_Campaign;
    }

    /**
     * index page
     */ 
    public function indexAction() 
    {
        $form = new Core_Form_Campaign_Search;
        $form->populate($_GET);
        $this->view->form = $form;
            
        $paginator = $this->_model->getPaginator($form->getValues(), $this->_getParam('sort'));
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
     * create new campaign
     */
    public function createAction()
    {
        $form = new Core_Form_Campaign_Create;
         
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {    
                $campaignId = $this->_model->create($form->getValues());
                $this->_helper->FlashMessenger('Campaign added'); 
                $this->_helper->redirector(
                    'viewdetails', 
                    'campaign', 
                    'default',
                    array(
                        'campaign_id' => $campaignId)
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
     * view campaign
     */
    public function viewdetailsAction()
    {   
        $campaignId = $this->_getParam('campaign_id');
        $this->_model->setCampaignId($campaignId);
        $this->view->campaign = $this->_model->fetch();
        $this->view->campaignId = $campaignId;
        $this->view->totalLeads = 
            $this->_model->getTotalNumberOfLeadsByCampaignId($campaignId);
        $this->view->totalOpportunities = 
            $this->_model->getTotalNumberOfOpportunitiesByCampaignId($campaignId);
        $this->view->totalContacts = 
            $this->_model->getTotalNumberOfContactsByCampaignId($campaignId);
        $this->view->totalAccounts = 
            $this->_model->getTotalNumberOfAccountsByCampaignId($campaignId);
        $this->view->totalInvoices = 
            $this->_model->getTotalNumberOfInvoicesByCampaignId($campaignId);
        $this->view->totalQuotes = 
            $this->_model->getTotalNumberOfQuotesByCampaignId($campaignId);
        $this->view->totalMessages = 
            $this->_model->getTotalNumberOfMessagesByCampaignId($campaignId);
    }

    /**
     * Edit Campaign item details
     */
    public function editAction()
    {
        $campaignId = $this->_getParam('campaign_id'); 
        $this->_model->setCampaignId($campaignId);
        
        $form = new Core_Form_Campaign_Edit($this->_model);
        $form->setAction($this->_helper->url(
                'edit', 
                'campaign', 
                'default',
                array(
                    'campaign_id'=>$campaignId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $this->_model->edit($form->getValues());
                $this->_helper->FlashMessenger('The campaign has been edited successfully');
                $this->_helper->redirector('viewdetails', 'campaign', 'default',
                    array('campaign_id'=>$campaignId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }

    public function deleteAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $deleted = $this->_model
                        ->setCampaignId($this->_getParam('campaign_id'))
                        ->delete();

        if ($deleted) {
           $message = 'The campaign was successfully deleted'; 
        } else {
           $message = 'The campaign could not be deleted'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('index', 'campaign', 'default');
    }

    public function jsonstoreAction()
    {
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('campaign_id', $items);
        $this->_helper->AutoCompleteDojo($data);

    }

} 
