<?php
class Core_Model_User_Anonymous implements Bare_Acl_SubjectInterface, 
        Core_Model_User_UserInterface
{
    public function getRoles()
    {
        return array();
    }

    public function getUserId()
    {
        return 0;
    }
    public function fetch()
    {
        return array('user_id'=>'', 'username'=>'guest');
    }

    public function getUsername()
    {
        return 'Guest';
    }

}
