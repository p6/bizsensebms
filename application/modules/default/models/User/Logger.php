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
 * You can account Binary Vibes Information Technologies Pvt. Ltd. by sending 
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
class Core_Model_User_Logger 
{
    public function update($observable)
    {
        $status = $observable->getStatus();
        $ephemeralData = $observable->getEphemeral();
        $logger = new Core_Service_Log;           
    
        switch ($status){
            case Core_Model_User::STATUS_AUTHENTICATE_SUCCESS:
                /**
                 * User has successfully logged on
                 */
                $user = new Core_Model_User;
                $userData = $user->fetch();
                $userEmail = $userData->email;
                $logger->info("Session opended for $userEmail");
                break;
            case Core_Model_User::STATUS_AUTHENTICATE_FAILURE:
                /**
                 * Unsuccessful login attempt
                 */
                $failedUser = $ephemeralData['authFailedUserName'];
                $logger->info("Failed login attempt for $failedUser");
                break; 

            case Core_Model_User::STATUS_PASSWORD_CHANGE_SUCCESS:
                $userData = $observable->fetch();
                $logger->info("User with ID " . $userData->user_id . " and email " 
                    . $userData->email . " has changed his/her password");
                break;
            
            case Core_Model_User::STATUS_LOGOFF :
                $userData = $ephemeralData['loggedOutUser'];
                $logger->info("Session closed for $userData->email");
                break;
        }
        
    }    
}
