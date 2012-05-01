<?php
/*
 * Edit my profile form
 *
 *
 * LICENSE: GNU GPL V3
 *
 * This source file is subject to the GNU GPL V3 license that is bundled
 * with this package in the file license
 * It is also available through the world-wide-web at this URL:
 * http://bizsense.binaryvibes.co.in/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@binaryvibes.co.in so we can send you a copy immediately.
 *
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @license    http://bizsense.binaryvibes.co.in/license   
 * @version    $Id:$
 */
class Profile_Form_Edit
{
    public $db;
	public $userId;

    public function __construct($userId)
    {
        $this->db = Zend_Registry::get('db');
		$this->userId = $userId;
    }
    
    public function getForm()
    {
		$profileUtil = new User($this->userId);
		$profile = $profileUtil->fetch();

        $form = new Zend_Form;
        $form->setAction('/profile/editmy')
                ->setMethod('post');

        $firstName = $form->createElement('text', 'firstName')
                            ->setLabel('First Name')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setRequired(true);

        $middleName = $form->createElement('text', 'middleName')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Middle Name');

        $lastName = $form->createElement('text', 'lastName')
                            ->addValidator(new Zend_Validate_StringLength(0, 100))
                            ->setLabel('Last Name')
                            ->setRequired(true);

        $personalEmail = $form->createElement('text', 'personalEmail')
                                ->addValidator(new Zend_Validate_StringLength(0, 320))
                                ->setLabel('Personal Email');

        $workPhone = $form->createElement('text', 'workPhone')
                            ->addValidator(new Zend_Validate_StringLength(0, 20))
                            ->setLabel('Work Phone');

        $mobilePhone = $form->createElement('text', 'mobilePhone')
                                ->addValidator(new Zend_Validate_StringLength(0, 20))
                                ->setLabel('Mobile Phone');

        $homePhone = $form->createElement('text', 'homePhone')
                            ->addValidator(new Zend_Validate_StringLength(0, 20))
                            ->setLabel('Home Phone');

        $branchId = new Zend_Dojo_Form_Element_FilteringSelect('branchId');
        $branchId->setLabel('Branch')
                ->setAutoComplete(true)
                ->setStoreId('branchStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/jsonstore/branch'))
                ->setAttrib("searchAttr", "branchName")
                ->setRequired(true);


        /*
         *  Load defaults
         */
        if ($profile) {
            $branchId->setValue($profile->branchId);
            $homePhone->setValue($profile->homePhone);
            $mobilePhone->setValue($profile->mobilePhone);
            $workPhone->setValue($profile->workPhone);
            $personalEmail->setValue($profile->personalEmail);
            $lastName->setValue($profile->lastName);
            $middleName->setValue($profile->middleName);
            $firstName->setValue($profile->firstName);
        }

        $submit = $form->createElement('submit', 'Submit');

        $form->addElements(array($firstName, $middleName, $lastName, $personalEmail, $workPhone, $mobilePhone,
            $homePhone, $branchId, $submit));

        return $form;

    }
}


