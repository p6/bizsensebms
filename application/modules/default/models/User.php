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
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_User extends Core_Model_Abstract
 implements Core_Model_User_UserInterface, Bare_Acl_SubjectInterface
{
    
    /**
     * @var table class name
     */
    protected $_dbTableClass = 'Core_Model_DbTable_User';

    /**
     * @var The names of the default observer classes
     */
    protected $_defaultObservers = array('Core_Model_User_Logger');

    /**
     * @Deprecated The user id
     * Use _userId instead
     */ 
    protected $_user_id;

    /**
     * @var the user ID
     */
    protected $_userId;

    /**
     * @var object the user record
     */
    protected $_record;

    /**
     * Status code
     */
    const USER_STATUS_CODE_ACTIVE = 1;
    const USER_STATUS_CODE_BLOCKED = 0;

    /** 
     * Define user types
     */
    const TYPE_1 = 'Employee';
    const TYPE_2 = 'Customer';
    const TYPE_3 = 'Partner';
    const TYPE_4 = 'Vendor';

    /**
     * Status constants the user
     */
    const STATUS_AUTHENTICATE_SUCCESS = 'User has authenticated successfully';
    const STATUS_LOGOFF = 'User has logged off';
    const STATUS_AUTHENTICATE_FAILURE = 'User failed to authenticate';
    const STATUS_PASSWORD_CHANGE_SUCCESS = 'User has changed his/her password';

    /**
     * @param $user_id is the user id
     * If the $user_id is not provided default to current user id
     */
    public function __construct($user_id = null)
    {
        parent::__construct();
       
        if (isset($user_id)) {
            $this->setUserId($user_id);
        } else {
            $this->setUserId(Core_Model_User_Current::getId());
        }
    }

    
    /**
     * Fetch one user
     */
    public function fetch()
    {
        if (!empty($this->_record)) {
            return $this->_record;
        }
        $db = $this->getTable()->getAdapter();
        $select = $db->select();
        $select->from(
            array('u'=>'user'), 
            array('user_id', 'username' ,'email', 'status', 'mode', 'hash', 'created', 
            'alt_email', 'host_created', 'ip_created', 'user_timezone')
        )
            ->joinLeft(
                array('p'=>'profile'),
                'p.user_id = u.user_id', array('*')
            )
            ->joinLeft(
                array('r'=>'role'),
                'r.role_id = p.primary_role', 
                array('r.name'=>'name as primaryRoleName')
            )
            ->joinLeft(
                array('urp'=>'user'),
                'urp.user_id = p.reports_to', 
                array('urp.email'=>'email as reportsToEmail')
            )   
            ->joinLeft(
                array('b'=>'branch'),
                'b.branch_id = p.branch_id', array('branch_name') 
            )
            ->where('u.user_id = ?', $this->_user_id);
        $result = $db->fetchRow($select);
        $this->_record = $result;
        return $result; 
    }

  
    /**
     * Create a user and profile
     * @param associative array $data of model keys and values
     */
    public function create($data = array())
    {
        /**
         * Filter the input data
         */
        if (!is_numeric($data['reports_to'])) {
            $data['reports_to'] = null;
        }   
        
        if (!is_numeric($data['primary_role'])) {
            $data['primary_role'] = null;
        }   
        
       /**
        * Insert values to user table 
        */
        $userData = array(
            'username' => $data['username'],
            'email'     => $data['email'],
            'password'  => md5($data['password']),
            'created'   =>  time(),
        );

        $user_id = parent::create($userData);
        $this->setId($user_id);

        /*
         * Create profile for the user
         */
        
        $profileData = $data;
        
        $profileData['user_id'] = $user_id;
        $profileModel = new Core_Model_User_Profile;
        $profileTable = $profileModel->create($profileData);

        
        /**
         * Primary role must be reflected in userRoles
         */
        $roleData = array(
            'user_id'   =>  $user_id, 
            'role_id'   =>  $data['primary_role']
        );

        $userRole = new Core_Model_UserRole;
        $userRole->create($roleData);
        /**
         * Notify the user about his/her account
         */
        if ($data['notify_user']) {
            $accountNotify = new Core_Model_User_Notify_Email_NewAccount();
            $accountNotify->update($this, $data['password']);
        }
    }

    /**
     * Set user Id
     * @Deprecated user setUserId() instead
     */
    public function setId($user_id)
    {
        return $this->setUserId($user_id);        
    }

    /**
     * @param int $userId the user id
     * @return object Core_Model_User
     */
    public function setUserId($userId)
    {
        if (isset($userId) and !is_numeric($userId)) {
            throw new Exception('Invalid user ID. The user ID must be an integer');
        }
        /**
         * user_id property is deprecated. Use userId instead
         */
        $this->_user_id = $userId;


        $this->_userId = $userId;
        return $this;
    
    }


    /**
     * Edit user's profile
     */
    public function editProfile($data)
    {

        $db = Zend_Registry::get('db');
        /**
         * Update the user table
         */
        $userData = array(
            'email'     =>  $data['email'],
        );

        /**
         * Update the password field only if it is specified and valid
         */
        if (strlen($data['password']) > 4) {
            $userData['password']  =  md5($data['password']);
        }

        $db->update('user', $userData, 'user_id = ' . $this->_user_id);

        /**
         * Update the profile table
         */
         $profileData = array(
            'first_name'     => $data['first_name'],
            'middle_name'    => $data['middle_name'],
            'last_name'      => $data['last_name'],
            'primary_role'   => $data['primary_role'],
            'branch_id'      => $data['branch_id'],
            'reports_to'     => $data['reports_to'], 
            'personal_email'  => $data['personal_email'], 
            'employee_number' => $data['employee_number'],
            'pf_number' => $data['pf_number'],
            'esi_number' => $data['esi_number']
        );
        
        if (!is_numeric($profileData['primary_role'])) {
            $profileData['primary_role'] = null;
        }

        if (!is_numeric($profileData['reports_to'])) {
            $profileData['reports_to'] = null;
        }

        $db->update(
            'profile', $profileData, 'user_id = ' . $this->_user_id
        );
        

    }

    /**
     * @return userId
     * @param $email = email address of the user
     */
    public static function getUserIdFromEmail($email)
    {
        $db = Zend_Registry::get('db');
        $result = $db->fetchOne(
            'SELECT user_id FROM user WHERE email = ?', $email
        );
        return $result;
    }


    /**
     * Update the password
     */
    public function updatePassword($data = array())
    {
        $table = $this->getTable(); 
        $data = $this->unsetNonTableFields($data);
        $data['password'] = md5($data['password']);
        $where = $table->getAdapter()
            ->quoteInto('user_id = ?', $this->_user_id);
        $this->setStatus(self::STATUS_PASSWORD_CHANGE_SUCCESS);
        $table->update($data, $where);
        
        return $this;
 
    }
   
    /**
     * Authenticate the user
     */ 
    public function authenticate($data = array())
    {
        $username = $data['username'];
        $password = $data['password'];
        
        $dbAdapter = Zend_Registry::get('db');
        
       
        $validator = new Zend_Validate_EmailAddress();
        if ($validator->isValid($username)) {
            $authAdapter = new Zend_Auth_Adapter_DbTable(
            $dbAdapter, 'user', 'email', 'password', 'MD5(?) AND status <> 0'
            );
        } else {
            $authAdapter = new Zend_Auth_Adapter_DbTable(
            $dbAdapter, 'user', 'username', 'password', 'MD5(?) AND status <> 0'
            );
        }       
        
        $authAdapter->setIdentity($username);
        $authAdapter->setCredential(($password));

        $auth = Zend_Auth::getInstance(); 
        $result =  $auth->authenticate($authAdapter);
      
        if ($result->isValid()) {
            $this->setStatus(self::STATUS_AUTHENTICATE_SUCCESS);
        } else {
            $this->_ephemeralData['authFailedUserName'] = $data['username'];
            $this->setStatus(self::STATUS_AUTHENTICATE_FAILURE);
        }   
        return $result; 
    
    }

    /**
     * Retrieve the user's password
     */
    public function retrievePassword($data = array())
    {
        $db = $this->getTable()->getAdapter();
        $email = $data['username'];
        $result = $db->fetchOne(
            "SELECT user_id FROM user WHERE email = ? AND status=1", $email
        );
        if ($result) {
            /**
             * Set flag
             */
            $hash = time() . $email;
            $hash = md5($hash);
            $data = array(
                'mode'      => 1,
                'hash'      => $hash
            );
            $where[] = "email = '$email'";
            $n = $db->update('user', $data, $where);

            $ip = $_SERVER['REMOTE_ADDR'];
            $url = "http://" . $_SERVER['SERVER_NAME'] 
                . "/user/resetpass?hash=$hash";
            $body = "Hello," . "\n\n" . "You are somebody else from the IP 
                address $ip requested to reset the password ";
            $body .= "for your account. To reset the password visit 
                the following link" . "\n\n" . $url;
            $from = "webmaster@" . $_SERVER['SERVER_NAME'];


            $mail = new Core_Service_Mail;
            $mail->setBodyText($body);
            $mail->addTo($email, 'BizSense user');
            $mail->setSubject("Recover password");
            $mail->send();
            $message = "Password reset initiated. Instructions to reset the 
                password has been sent to your email ";
            $message .= "address.";
            $viewMessage = $message;

        } else {
            $viewMessage = "Password reset could not be initiated. 
                Contact your administrator.";
        }
        return $viewMessage;
 
    }

    /**
     * Verify if the hash sent in request password action is genuine
     * @return boolean true if hash is genuine
     * @param $hash is the hash string in the URI
     */
    public function verifyResetPasswordHash($hash = null)
    {
        $db = $this->getTable()->getAdapter();
        $resetPasswordNamespace = new Zend_Session_Namespace(
            'resetPasswordNamespace'
        );

        $email = $db->fetchOne(
            "SELECT email FROM user WHERE hash= ? AND mode = 1", $hash
        );

        if ($email) {
            $resetPasswordNamespace->set = 1;
            $resetPasswordNamespace->hash = $hash;
            $resetPasswordNamespace->email = $email;
            $resetPasswordNamespace->showForm = true;
            return true;
        }

        return false;
    }

    /**
     * Reset the user's password
     * Update the session name space about the ability to reset password
     */
    public function resetPassword($data = array())
    {
        $db = $this->getTable()->getAdapter();
        $resetPasswordNamespace = new Zend_Session_Namespace(
            'resetPasswordNamespace'
        );
        $email = $resetPasswordNamespace->email;
        unset($data['submit']);
        unset($data['Submit']);
        unset($data['password_confirm']);
        $data['password'] = md5($data['password']);     
        $db->update('user', $data, "email =  '$email'");
    }

    /**
     * Edit the user
     */
    public function edit($data = array())
    {
        $table = $this->getTable(); 
        $fields = $table->info(Zend_Db_Table_Abstract::COLS);
        if (isset($data['password'])) {
            $data['password'] = md5($data['password']);
        }
        foreach ($data as $field => $value) {
            if (!in_array($field, $fields)) {
                unset($data[$field]);
            }
        }
        $where = $table->getAdapter()
            ->quoteInto('user_id = ?', $this->_user_id);
        $table->update($data, $where);
       
    }


    /**
     * Set the permission to the role
     * @param array $data privilege_id
     */
    public function setPermission(
        $data = array(), $role_id = null, $resource_id = null
    )
    {
        $accessTable = new Core_Model_DbTable_Access;
        $resourceTable = new Core_Model_DbTable_Resource;
        $privilegeTable = new Core_Model_DbTable_Privilege;
        $roleTable = new Core_Model_DbTable_Role;

        if (is_numeric($resource_id)) {
            $where = $privilegeTable->getAdapter()
                ->quoteInto('resource_id = ?', $resource_id);
            $select = $privilegeTable->select();
            $select->from('privilege', 'privilege_id')
                ->where('resource_id = ?', $resource_id);
            $privilegeRowSet = $privilegeTable->fetchAll($select);

            foreach ($privilegeRowSet as $value) {
                $privilege = $value->toArray();
                $privilegeToDelete = $privilege['privilege_id'];
                $where = $accessTable->getAdapter()
                    ->quoteInto(
                        "role_id = ? and privilege_id = $privilegeToDelete", 
                        $role_id
                    );
                $accessTable->delete($where);
            }

        } else {
            $where = $accessTable->getAdapter()
                ->quoteInto("role_id = ?", $role_id);
            $accessTable->delete($where);

        }

        $dataToInsert = array();
        foreach ($data as $key=>$value) {
            if ($value == 1) {
                $dataToInsert = array(
                    'privilege_id' => $key, 
                    'role_id' => $role_id
                );
                $accessTable->insert($dataToInsert);
            }
        }
    }

    /**
     * Logg off from the session
     */
    public function logout()
    {
        $this->_ephemeralData['loggedOutUser'] = $this->fetch();
        $this->setStatus(self::STATUS_LOGOFF);
        Zend_Auth::getInstance()->clearIdentity();
    }
    
    /**
     * @return array roles of the user
     */
    public function fetchRoles()
    {
        $userRole = new Core_Model_UserRole;                
        return $userRole->fetchRoles($this->_user_id);
    }

    /**
     * Get the user's profile object
     * 
     * @return object Core_Model_Profile
     */
    public function getProfile()
    {   
       $profile = new Core_Model_User_Profile;
       $profile->setUserId($this->_user_id);
       return $profile;
    }

    /**
     * @return the user name 
     * @TODO to be implented
     */
    public function getUsername()
    {
        return "none";
    }
   
    /**
     * @return int the user Id
     */
    public function getUserId()
    {
        $record = $this->fetch();
        return $record->user_id;
    }


    /**
     * @return array roles to which the user belongs
     */
    public function getRoles()
    {
        $userRoleTable = new Core_Model_DbTable_UserRole();
        $select = $userRoleTable->select();
        $select->setIntegrityCheck(false);
        $select->from('user_role', array())
                ->join('user', 'user.user_id = user_role.user_id', array())
                ->join('role', 'user_role.role_id = role.role_id', array('role.name as role'));
        $result = $userRoleTable->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        } else {
            $result = array();
        }
        $toReturn = array();
        foreach ($result as $row) {
            $toReturn[] = $row['role'];
        }

        if ($this->_userId) {
            $toReturn[] = 'authenticated';
        }
        return $toReturn;

    }

    /**
     * @return int branch ID
     */
    public function getBranchId()
    {
        $record = $this->fetch();
        return $record->branch_id;
    }

    /*
     * @return array of user records
     */
    public function fetchAll()
    {
        $db = $this->getTable()->getAdapter();
        $sql = "SELECT email, user_id FROM user";
        $result = $db->fetchAll($sql);
        return $result;

    }
   
    /**
     * Edit the roles of the user
     * @param array roles
     */
    public function editRole($inputData)
    {
        $db = $this->getTable()->getAdapter();
        $where = $db->quoteInto("user_id = ?", $this->_userId);
        $db->delete('user_role', $where);
        $role = $inputData['role'];
        $status = $inputData['status'];
        if ($role) {		
		    foreach ($role as $key=>$value) {
			    if ($value == 1) {
				    $data = array(
					    'user_id' => $this->_userId,
					    'role_id' => $key
				    );
				    $db->insert('user_role', $data);
				}
			}
		}	
       $data = array('status'=>$status); 
       $db->update('user', $data, $where);

    }

    /**
     * @return string email address of the user
     */
    public function getEmail()
    {
        $record = $this->fetch();
        return $record->email;
    }
    
    /**
     * @return int primary role id
     */
    public function getPrimaryRoleId()
    {
        $profileModel = new Core_Model_User_Profile;
        $result = $profileModel->getPrimaryRoleId();
        return $result;
    }
    
}

