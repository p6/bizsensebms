<?php
/*
 * Field Value Form
 *
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
 * You can contact Binary Vibes Information Technologies Pvt. Ltd. by sending an electronic mail to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class BV_Form_FieldValue
{
    public $db;
    protected $_action;
    protected $_nameLabel = 'Name';
    protected $_descriptionLabel = 'Description';
    protected $_nameValue = '';
    protected $_descriptionValue = '';

    public function __construct($action = null)
    {
        $this->db = Zend_Registry::get('db');
        $this->_action = $action;
    }

    public function setAction($action)
    {
        $this->_action = $action;
    }

    public function setValues($name = '', $description = '')
    {
        $this->_nameValue = $name;
        $this->_descriptionValue = $description;
    }

    public function getAddForm()
    {
        $form = new Zend_Form;
        $form->setAction($this->_action);
        $form->setMethod('post');

        $name = $form->createElement('text', 'name')
                    ->setLabel($this->_nameLabel)
                    ->setValue($this->_nameValue)
                    ->setRequired(true);

        $description = $form->createElement('textarea', 'description')
                        ->setLabel('Description')
                        ->setValue($this->_descriptionValue)
                        ->setAttribs(array('rows'=>'3', 'cols'=>'40'));

        $submit = $form->createElement('submit', 'submit',array (
                    'class' => 'submit_button'
                ));
        
        $form->addElements(array($name, $description, $submit));
        
        return $form;
                        
    }
}


