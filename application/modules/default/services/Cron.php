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
class Core_Service_Cron extends Core_Service_CliAbstract 
    implements Core_Service_CliInterface
{

    /**
     * @param array cron observers 
     */
    protected $_cronClasses = array(
        'Core_Model_Newsletter_Message_Queue',
        //'Core_Model_Activity_Task_Reminder',
        //'Core_Model_Activity_Call_Reminder',
        //'Core_Model_Activity_Meeting_Reminder',
    );
   
    /**
     * @return void 
     */
    public function run()
    {
        $variableModel = new Core_Model_Variable; 
        $cronstatusModel = new Core_Model_CronStatus;
        $canRun = (int) $cronstatusModel->cronLockStatus();

        if ($canRun != Core_Model_CronStatus::CORE_SERVICE_CRON_LOCK) {
            $variableModel->save('core_service_cron_lock', 1);
            $this->message('Cron is initilized');
            $this->_run();
            $variableModel->save('core_service_cron_lock', 0);
            $this->message('Cron completed');
        } else {
            $this->message('Cron is locked. Aborting.');
        }

    }

    protected function _run()
    {
         foreach ($this->_cronClasses as $class) {
            $object = new $class;
            $object->cron();
         }
    }


}
