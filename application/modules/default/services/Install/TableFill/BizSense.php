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
 * You can contact Binary Vibes Information Technologies Pvt. 
 * Ltd. by sending an electronic mail to info@binaryvibes.co.in
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
class Core_Service_Install_TableFill_BizSense
{
    /**
     * The data posted via the install form
     */
    protected $_data = array();
    
    /**
     * The Zend_Db database adapter
     */
    protected $_db;

    /**
     * The application Acl object
     */
    protected $_acl;

    /**
     * Initialize the object
     */
    public function __construct($data = array())
    {
        $this->_data = $data;
        $this->_db = Zend_Registry::get('db');
        $this->createAdminUser();
        $this->fillVersion();
        $this->fillAcl();
        $this->fillUrlAccess();
        $this->fillApplicationData();     
        $this->fillCronLockStatus();

    }

    /**
     * Create the admin user
     */
    public function createAdminUser()
    {
        date_default_timezone_set('Asia/Calcutta');
        
        $data = array(
            'email'         =>  $this->_data['admin_email'],
            'username'  => $this->_data['username'],
            'password'      =>  md5($this->_data['password']),
            'created'       =>  time(),
        );

        
        $this->_db->insert('user', $data);        
    }
   
    /**
     * Insert the application version number in the database
     */ 
    public function fillVersion()
    {
        $data = array(
            'name' => 'version',
            'value' => '0.3.1-Alpha',
        );

        $result = $this->_db->insert('variable', $data);
    }

    /**
     * Insert default cron lock value in the database
     */
    public function fillCronLockStatus()
    {
        $data = array(
            'name' => 'core_service_cron_lock',
            'value' => '0',
        );

        $this->_db->insert('variable', $data); 
    }

    /**
     * Create resource and privileges
     */
    public function fillAcl()
    {

        $resourceModel = new Core_Model_Resource;
        $privilegeModel = new Core_Model_Privilege;

        $privilegeFile = file_get_contents(APPLICATION_PATH . '/modules/default/services/Install/TableFill/Privileges.json');
        $privileges = json_decode($privilegeFile);
        foreach ($privileges as $privilege) {
            $privilegeModel->create(array('name'=> $privilege));
        }

    }

    /**
     * Fill the url_access table
     */
    public function fillUrlAccess()
    {
        $resourceModel = new Core_Model_Resource;
        $privilegeModel = new Core_Model_Privilege;
        $urlAccessModel = new Core_Model_UrlAccess;

        $urlAccessFile = file_get_contents(APPLICATION_PATH . '/modules/default/services/Install/TableFill/UrlAccess.json');
        $urlAccessContent = json_decode($urlAccessFile);
        if (!is_array($urlAccessContent)) {
            return;
        }
        if (!count($urlAccessContent)) {
            return;
        }

        foreach ($urlAccessContent as $record) {
            $urlAccessModel->insertByPrivilegeName((array) $record);
        }

    }

    /**
     * Insert the bulk of the application data
     * Like resources, privileges and url accesses
     */
    public function fillApplicationData()
    {
       
        $db = $this->_db;               

        /**
         * Instantiate the required objects to insert data
         */              
        $role = new Core_Model_Role;
        $userRole = new Core_Model_UserRole;
        $resource = new Core_Model_Resource;
        $privilege = new Core_Model_Privilege;        
        $urlAccess = new Core_Model_UrlAccess;

        

        /**
         * Add a default role
         */
        $defaultRoleQuery = "INSERT INTO `role` (
            `role_id` , `name` , `description` )
            VALUES ( NULL , 'default', 'default' );";
        $db->getConnection()->exec($defaultRoleQuery);




        $leadSourceValue = "INSERT INTO `lead_source` (
            `name` ) VALUES ('Other'), ( 'Cold Call'), ('Existing Customer'), ('Self Generated'), (
            'Employee'), ('Partner'), ('Public Relations'), ('Direct Mail'), ('Conference'
            ), ('Tradeshow'), ('Website'), ('Word of mouth'), ('Email'), ('Campaign')";
        $db->getConnection()->exec($leadSourceValue);
            
        

        $leadStatusValue = " INSERT INTO `lead_status` (`name`) VALUES ('Other'), ('New'), (
            'Assigned'), ('In process'), ('Dead')";

        $db->getConnection()->exec($leadStatusValue);
  
        $salutationValue = "INSERT INTO `salutation` (`name`) VALUES ('Mr'), ('Ms'), ('Dr'), ('Prof')";
        $db->getConnection()->exec($salutationValue);
              
       /**
        * Default value for sales stage
        */
        $salesStageValue = "INSERT INTO `sales_stage` (
            `name`, `context` ) VALUES ( 'Prospecting', 0), ('Qualification', 0), ('Needs Analysis', 0),
             ('Value Proposition', 0), ('Identifiying Decision Makers', 0), ('Perception Analysis', 0),
             ('Proposal/Price Quote', 0), ('Negotion/Review', 0), ('Closed Won', 1), ('Closed Lost', 2)";
        $db->getConnection()->exec($salesStageValue);
                    
        /*
         * Insert organization details
         * Create default branch
         */
        $data = array(
            'company_name'   =>  $this->_data['company_name'],
        ); 
        $db->insert('organization_details', $data);    
       
        $data = array(
            'branch_name'    =>  $this->_data['branch_name'],
            'address_line_1'  =>  $this->_data['address_line_1'],
            'address_line_2'  =>  $this->_data['address_line_2'],
            'address_line_3'  =>  $this->_data['address_line_3'],
            'address_line_4'  =>  $this->_data['address_line_4'],
            'city'          =>  $this->_data['city'],
            'state'         =>  $this->_data['state'],
            'postal_code'    =>  $this->_data['postal_code'],
            'country'       =>  $this->_data['country'],
       );              
            
       $db->insert('branch', $data);                

        /**
         * Create employee profile for admin
         */
        $data = array(  
            'first_name'     =>  $this->_data['first_name'],
            'middle_name'    =>  $this->_data['middle_name'],
            'last_name'      =>  $this->_data['last_name'],
            'user_id'           =>  1,
            'branch_id'      => 1,
        );
        
        $result = $db->insert('profile', $data);

        /**
         * Default ticket statuses
         */
        $data = array(
            array('name'=>'new', 'closed_context'=>0),
            array('name'=>'open', 'closed_context'=>0),
            array('name'=>'assigned', 'closed_context'=>0),
            array('name'=>'closed', 'closed_context'=>1),
        );

        foreach ($data as $datum) {
            $db->insert('ticket_status', $datum);                
        }

        /**
         * Default task statuses
         */
        $data = array(
            array('name'=>'Not Started', 'closed_context'=>0),
            array('name'=>'In Progress', 'closed_context'=>0),
            array('name'=>'Completed', 'closed_context'=>1),
            array('name'=>'Deferred', 'closed_context'=>1),
        );

        foreach ($data as $datum) {
            $db->insert('task_status', $datum);                
        }

        /**
         * Default call statuses
         */
        $data = array(
            array('name'=>'Scheduled', 'context'=>0),
            array('name'=>'Held', 'context'=>1),
            array('name'=>'Not Held', 'context'=>1),
        );

        foreach ($data as $datum) {
            $db->insert('call_status', $datum);                
        }

        /**
         * Default meeting statuses
         */
        $data = array(
            array('name'=>'Scheduled', 'context'=>0),
            array('name'=>'Held', 'context'=>1),
            array('name'=>'Not Held', 'context'=>1),
        );

        foreach ($data as $datum) {
            $db->insert('meeting_status', $datum);                
        }


        $this->fillFinanceData();
    }

    public function fillFinanceData()
    {
        $financeGroupCategoryModel = new Core_Model_Finance_Group_Category;
        $data = array (
            'Liability',
            'Assets',
            'Profit & Loss',
            'Capital Account',
            'Loans',
            'Current Liabilities',
            'Current Assets'
        );
        foreach ($data as $datum) {
            $financeGroupCategoryModel->create(array('name'=>$datum));
        }

        $financeGroupModel = new Core_Model_Finance_Group;
        $data = array(
            array(
                'name' => 'Capital Account',
                'category' => 'Liability'
            ),
            array(
                'name' => 'Loans',
                'category' => 'Liability'
            ),
            array(
                'name' => 'Current Liabilities',
                'category' => 'Liability',
            ),
            array(
                'name' => 'Fixed Assets',
                'category' => 'Assets',
            ),
            array(
                'name' => 'Investments',
                'category' => 'Assets',
            ),
            array(
                'name' => 'Current Assets',
                'category' => 'Assets',
            ),
            array(
                'name' => 'Miscellaneous Expenses',
                'category' => 'Assets',
            ),
            array(
                'name' => 'Suspense Account',
                'category' => null,
            ),
            array(
                'name' => 'Sales Accounts',
                'category' => 'Profit & Loss',
            ),
            array(
                'name' => 'Purchase Accounts',
                'category' => 'Profit & Loss',
            ),
            array(
                'name' => 'Direct Incomes',
                'category' => 'Profit & Loss',
            ),
            array(
                'name' => 'Indirect Incomes',
                'category' => 'Profit & Loss',
            ),
            array(
                'name' => 'Direct Expenses',
                'category' => 'Profit & Loss',
            ),
            array(
                'name' => 'Indirect Expenses',
                'category' => 'Profit & Loss',
            ),
            array(
                'name' => 'Reserves & Surplus',
                'category' => 'Capital Account',
            ),
            array(
                'name' => 'Bank OD Account',
                'category' => 'Loans',
            ),
            array(
                'name' => 'Secured Loans',
                'category' => 'Loans',
            ),
            array(
                'name' => 'Unsecured Loans',
                'category' => 'Loans',
            ),
            array(
                'name' => 'Duties And Taxes',
                'category' => 'Current Liabilities',
            ),
            array(
                'name' => 'Provisions',
                'category' => 'Current Liabilities',
            ),
            array(
                'name' => 'Sundry Creditors',
                'category' => 'Current Liabilities',
            ),
            array(
                'name' => 'Stock In Hand',
                'category' => 'Current Assets',
            ),
            array(
                'name' => 'Deposits',
                'category' => 'Current Assets',
            ),
            array(
                'name' => 'Loans And Advances',
                'category' => 'Current Assets',
            ),
            array(
                'name' => 'Sundry Debtors',
                'category' => 'Current Assets',
            ),
            array(
                'name' => 'Cash In Hand',
                'category' => 'Current Assets',
            ),
            array(
                'name' => 'Bank Accounts',
                'category' => 'Current Assets',
            ),
            array(
                'name' => 'Salaries Payable',
                'category' => 'Current Liabilities',
            )

        );
        foreach ($data as $datum) {
            if (null != $datum['category']) {
                $groupCategoryRecord = $financeGroupCategoryModel->fetchByName($datum['category']);
                $groupCategoryId = $groupCategoryRecord['fa_group_category_id'];
            } else {
                $groupCategoryId = null;
            }
            $dataToInsert = array(
                'name' => $datum['name'],
                'fa_group_category_id' => $groupCategoryId
            );
            $financeGroupModel->create($dataToInsert);

        }

       $ledger = new Core_Model_Finance_Ledger;
       $accountGroup = new Core_Model_Finance_Group;

       $ledger->create(array
            (
                'name' => 'Sales Account',
                'fa_group_id' => $accountGroup->getGroupIdByName('Sales Accounts'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'Purchase Account',
                'fa_group_id' => $accountGroup->getGroupIdByName('Purchase Accounts'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );

        $ledger->create(array
            (
                'name' => 'Indirect expense',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Expenses'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );

        $ledger->create(array
            (
                'name' => 'Indirect income',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Incomes'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $providentFund = $ledger->create(array
            (
                'name' => 'Provident fund',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Expenses'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $professionalTax = $ledger->create(array
            (
                'name' => 'Professional tax',
                'fa_group_id' => $accountGroup->getGroupIdByName('Duties And Taxes'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ESI = $ledger->create(array
            (
                'name' => 'ESI',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Expenses'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $incomeTax = $ledger->create(array
            (
                'name' => 'Income tax',
                'fa_group_id' => $accountGroup->getGroupIdByName('Duties And Taxes'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'TDS',
                'fa_group_id' => $accountGroup->getGroupIdByName('Duties And Taxes'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'Salaries and Wages',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Expenses'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'Discount received',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Incomes'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'Discount allowed',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Expenses'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'Freight Inward',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Expenses'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'Freight Outward',
                'fa_group_id' => $accountGroup->getGroupIdByName('Indirect Expenses'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        
        $ledger->create(array
            (
                'name' => 'Current Asset',
                'fa_group_id' => $accountGroup->getGroupIdByName('Current Assets'),
                'opening_balance_type' => Core_Model_Finance_Ledger::OPENING_BALANCE_TYPE_DEBIT,
                'opening_balance' => 0,
            )
        );
        

        $payslipField = new Core_Model_Finance_PayslipField;
        
        $payslipField->create(array
            (
                'name' => 'Basic salary',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => '',
                'machine_name' => 'basic_salary'
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'special_allowance',
                'name' => 'Special allowance',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'hra',
                'name' => 'HRA',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'personal_pay',
                'name' => 'Personal pay',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'dearness_allowance',
                'name' => 'Dearness allowance',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
               
        $payslipField->create(array
            (
                'machine_name' => 'medical_allowance',
                'name' => 'Medical allowance',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'shift_allowance',
                'name' => 'Shift allowance',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'cca',
                'name'  => 'CCA',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );$payslipField->create(array
            (
                'machine_name' => 'transport_allowance',
                'name' => 'Transport allowance',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'leave_travel_allowance',
                'name' => 'Leave travel allowance',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'performance_allowance',
                'name' => 'Performance allowance',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'canteen_subsidy',
                'name' => 'Canteen subsidy',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'special_living_allowance',
                'name' => 'Special living allowance',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'other_allowances',
                'name' => 'Other allowances',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'salary_arrears',
                'name' => 'Salary arrears',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::EARNING_FIELDS,
                'ledger_id' => ''
            )
        );
                
        $payslipField->create(array
            (
                'machine_name' => 'provident_fund',
                'name' => 'Provident fund',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS,
                'ledger_id' => $providentFund
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'voluntary_pf',
                'name' => 'Voluntary PF',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS,
                'ledger_id' => $providentFund
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'professional_tax',
                'name' => 'Professional Tax',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS,
                'ledger_id' => $professionalTax
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'esi',
                'name' => 'ESI',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS,
                'ledger_id' => $ESI
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'income_tax',
                'name' => 'Income tax',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_TAX_FIELDS,
                'ledger_id' => $incomeTax
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'rent_recovery',
                'name' => 'Rent recovery',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS,
                'ledger_id' => '' 
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'loan_deduction',
                'name' => 'Loan deduction',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS,
                'ledger_id' => '' 
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'insurance_deduction',
                'name' => 'Insurance deduction',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS,
                'ledger_id' => '' 
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'club_deductions',
                'name' => 'Club deductions',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS,
                'ledger_id' => '' 
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'provident_fund_arrears',
                'name' => 'Provident fund arrears',
                'enabled' => '0',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS,
                'ledger_id' => '' 
            )
        );
        
        $payslipField->create(array
            (
                'machine_name' => 'advance',
                'name' => 'Advance',
                'enabled' => '1',
                'type' => Core_Model_Finance_PayslipField::DEDUCTION_NON_TAX_FIELDS,
                'ledger_id' => '' 
            )
        );
        
        $data = array(
            'name' => 'newsletter_message_queue_settings_threshold_bounce_message',
            'value' => '2',
        );

        $this->_db->insert('variable', $data); 
        
        $variableModel = new Core_Model_Variable;
        $variableModel->save('newsletter_message_queue_settings_bounce_time_settings', "12:00:00");
        
    }
        
}
