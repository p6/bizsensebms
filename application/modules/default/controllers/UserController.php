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

class UserController extends Zend_Controller_Action 
{ 
    protected $_model;
    
    public function init()
    {
        $this->_model = new Core_Model_User;
    }

    /**
     * Provide a list of users to XHR requests
     */
    public function jsonstoreAction()
    {
        $items = (array) $this->_model->fetchAll();
        $data = new Zend_Dojo_Data('user_id', $items);
        $this->_helper->AutoCompleteDojo($data);
    }
 
    /**
     * Authenticate the user 
     */	
    public function loginAction() 
    { 
        $this->_helper->layout->setLayout('login');
        $form = new Core_Form_User_Login;
        $form->setAction(
            $this->_helper->url(
                'login',
                'user', 
                'default' 
                )
        );
	    if ($this->_request->isPost()){
   	        if ($form->isValid($_POST)){
	            $result = $this->_model->authenticate($form->getValues());    
                if ($result->isValid()){
		            $this->_helper->FlashMessenger(
                        'Welcome to Binary Vibes BizSense'
                    );
                    $this->_helper->redirector('index', 'index', 'default');
                } else {
                    $this->view->message = 
                        'Invalid login. Please try once again.';
                }
	        } else {
               $form->populate($_POST);
		
	        }
               $this->view->form = $form;
        }
        $this->view->form = $form;

    } 

    /**
     * Change account password
     */
    public function changepassAction() 
    { 
        $form = new Core_Form_User_ChangePassword;
        $currentUser = $this->view->currentUser;
        $currentUserId = $currentUser->getUserId();

	    if ($this->_request->isPost()){
	
   	        if ($form->isValid($_POST)){
                $this->_model->setId($currentUserId)
                        ->updatePassword($form->getValues());
		        $this->_helper->FlashMessenger('Password updated');
                $this->_helper->redirector('index', 'index', 'default');
	        } 
            $form->populate($_POST);
        } 
        $this->view->form = $form;
    }

    /**
     * Request password reset link
     */ 
    public function forgotpassAction() 
    {
        $form = new Core_Form_User_ForgotPassword;
	    if ($this->_request->isPost()){
   	        if ($form->isValid($_POST)){
                $this->view->message = 
                    $this->_model->retrievePassword($form->getValues());
	        } else {
               $form->populate($_POST);
               $this->view->form = $form;
		
	        }
   	
        } else {
            $this->view->form = $form;
	    }
    } 
    
    /**
     * Reset password
     */
    public function resetpassAction()
    {
		$resetPasswordNamespace = 
            new Zend_Session_Namespace('resetPasswordNamespace');
        $this->_model->verifyResetPasswordHash($this->_getParam('hash'));    

		if ($resetPasswordNamespace->set == 1) {
		/*
         * Session is set. Link is valid. 
         */
            $userForm = new Core_Form_User_PasswordReset;
            $form = $userForm->getForm();

		    if ($this->_request->isPost()){
			    if ($form->isValid($_POST)){
                    $this->_model->resetPassword($form->getValues());
			        $message = 
                        sprintf("Password is reset. 
                            You may now <a href='/user/login'>login</a> 
                            with your new password"
                                );
				    $this->view->message = $message;
			    } else {
		            $form->populate($_POST);
	                $this->view->form = $form;
			    }
		    } else {
		
		        $this->view->form = $form;
		    } 
		
		}
		
    }

    /**
     * Logout from the current session
     */
    public function logoutAction()
    {
        $this->_model->logout();
        $this->_helper->redirector("login", "user", "default");
    }

    /**
     * List of paginated, searchable and sortable roles
     */
    public function rolesAction()
    {
        $roleModel = new Core_Model_Role();
        $paginator = $roleModel->getPaginator(
            $this->_getParam('search'), $this->_getParam('sort')
        );
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;
    }

    /**
     * Delete role
     */
    public function deleteroleAction()
    {        
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        $role = new Core_Model_Role($this->_getParam('role_id'));
        $deleted = $role->delete();

        if ($deleted) {
           $message = 'Role deleted'; 
        } else {
           $message = 'Role could not be deleted. This role is assigned to users'; 
        } 
        $this->_helper->flashMessenger($message);
        $this->_helper->redirector('roles', 'user', 'default');

    }
    

    /**
     * List the users
     */
    public function usersAction()
    {
        $form = new Core_Form_User_Search;
        $form->populate($_POST);
        $this->view->form = $form;

        $sort = $this->_getParam('sort');
        $search = $form->getValues();

        $paginator = $this->_model->getPaginator($search, $sort);
      
        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage(25);
        $this->view->paginator = $paginator;

	
   }

    /**
     * Create a user
     */
    public function addAction()
    {
        $form = new Core_Form_User_AddUser;
        $form->setAction($this->view->url(array(
                'module'        =>  'default',
                'controller'    =>  'user',
                'action'        =>  'add',
            )
        ));
	    if ($this->_request->isPost()){
	        if ($form->isValid($_POST)){
                $user = new Core_Model_User;
                $user->create($form->getValues());
		        $this->_helper->FlashMessenger('User and profile were created');
                $this->_helper->redirector('users', 'user', 'default');
	        } else {
                $form->populate($_POST);
                $this->view->form = $form;
	        }
   	
        } else {
            $this->view->form = $form;
	    }
	
    }

    /**
     * Edit the user details
     */
    public function editAction()
    {
	    $uid = $this->_getParam("user_id");
        $this->_model->setUserId($uid);
        $this->view->uid = $uid;

        if ($uid == 1) {
            return;
        }    

        $form = new Core_Form_User_EditUser($uid);
	    if ($this->_request->isPost()){
   	        if ($form->isValid($_POST)){
                $this->_model->editRole($form->getValues());
                $message = 'User information was successfully edited';
		        $this->_helper->FlashMessenger($message);
                $this->_helper->redirector('users', 'user', 'default');
	        }
               $form->populate($_POST);
               $this->view->form = $form;
        } else {
            $this->view->form = $form;
	    }

    }

    /**
     * Create a role
     */
    public function addroleAction()
    {
        $form = new Core_Form_User_AddRole;

        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
                $model = new Core_Model_Role;
                $model->create($form->getValues());
                $this->_helper->FlashMessenger('Role successfully added');
                $this->_helper->redirector('roles', 'user', 'default');
            } else {
                $form->populate($_POST);
                $this->view->form = $form;
            }

        } else {
                $this->view->form = $form;		
	    }			
    }

    /**
     * Set privileges access to roles
     */
    public function permissionsAction()
    {
        $roleId = $this->_request->getParam('role_id');
        $roleModel = new Core_Model_Role($roleId);
        $this->view->roleRecord = $roleModel->fetch();
        $form = new Core_Form_User_Permission($roleId);
        $url = $this->_helper->Url('permission', 'user', 'default');

        if ($this->_request->isPost()){
            if ($form->isValid($_POST)){
               /**
                * A bug seems to be introduced in ZF 1.10.4 
                * $form->getValues() returns array starting with index 0
                * We cannot have an array starting with index 0. The index must start from 1
                */
               #$privileges = $form->getValues();
                $privileges = $_POST;
                $this->_model->setPermission($privileges['privilege'], $roleId);
                $this->_helper->FlashMessenger('Permission successfully set');
                $this->_helper->Redirector(
                    'roles',
                    'user',
                    'default'
                );
            } 
        }
        $this->view->form = $form;

	}

    /**
     * Set timezone for the user
     */
    public function timezoneAction()
    {
        $uid = $this->_getParam('user_id');
        $tForm = new Core_Form_User_SetTimeZone($uid);
        $form = $tForm->getForm();

        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                $this->_model->setId($uid)->edit($form->getValues());
                $message = 'User\'s timezone has been changed successfully';
                $this->_helper->FlashMessenger($message);
                $this->_helper->Redirector(
                    'edit', 
                    'user', 
                    'default', 
                    array('user_id' => $uid)
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
     * View details of the user
     */
    public function viewdetailsAction() 
    {
        $uid = $this->_getParam('user_id');
        if (is_numeric($uid)) {
            $user = new Core_Model_User($uid);
            $userData = $user->fetch();
            $this->view->userData = $userData;   
            $this->view->memberOfRoles = $user->fetchRoles();   
        }
    }

    /**
     * Edit the user's profile
     */
    public function editprofileAction()
    {
        $userId = $this->_getParam('user_id');
        $currentUser = $this->view->currentUser;
        $currentUserId = $currentUser->getUserId();
        $form = new Core_Form_User_EditProfile($userId);

	    if ($this->_request->isPost()) {
	        if ($form->isValid($_POST)) {
                $user = new Core_Model_User($userId);
                $user->editProfile($form->getValues());
		        $this->_helper->FlashMessenger('User\'s profile was edited');		
                if ($currentUserId === $userId) {
                    /**
                     * If the user's email address changes the bootstrap user
                     * resource looks for old data. So we have to logout the 
                     * current user. Refer to issue#332
                     */
                    $this->_model->logout();
                    $this->_helper->redirector('login', 'user', 'default');
                } else {
                    $this->_helper->redirector('users', 'user', 'default');
                }
	        } else {
                $form->populate($_POST);
                $this->view->form = $form;
	        }
   	
        } else {
            $this->view->form = $form;
	    }
    }

    /**
     * Edit the role's name
     */
    public function editroleAction()
    {
        $roleId = $this->_getParam('role_id');
        $role = new Core_Model_Role($roleId);
        
        $form = new Core_Form_User_EditRole($roleId);
        $form->setAction($this->view->url(
            array(
                'module' => 'default', 
                'controller' => 'user',
                'action' => 'editrole',
                'role_id' => $roleId
            ), null, true
        ));

        if ($this->_request->isPost()){
	        if ($form->isValid($_POST)){
                $role->edit($form->getValues());
		        $this->_helper->FlashMessenger('Role was edited');		
                $this->_helper->redirector('roles', 'user', 'default');
	        } else {
                $form->populate($_POST);
                $this->view->form = $form;
	        }
   	
        } else {
            $form->populate($role->fetch());   
            $this->view->form = $form;
            
	    }
   
    }
    
    public function initializeledgerAction()
    {
        $userId = $this->_getParam('user_id'); 
          
        $form = new Core_Form_Finance_Ledger_InitializeLedger;
        $form->setAction($this->_helper->url(
                'initializeledger', 
                'user', 
                'default',
                array(
                    'user_id'=>$userId
                )
            )
        );
        $this->view->form = $form;
       
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $profileModel = new Core_Model_User_Profile;
                $profileModel->initializeLedger($form->getValues());
                $this->_helper->FlashMessenger('The Ledger Initialized successfully');
                $this->_helper->redirector('viewdetails', 'user', 'default',
                    array('user_id'=>$userId));
            } else {
                $form->populate($this->getRequest()->getPost());
            }
        } 
    }
    	
}
