<?php
/**
 * Form to edit tax type
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
class Core_Form_Tax_EditTaxType extends Core_Form_Tax_AddTaxType
{
    protected $_taxTypeId;

    public function __construct($taxTypeId)
    {
        if (is_numeric($taxTypeId)) {
            $this->_taxTypeId = $taxTypeId;
        }
        parent::__construct();
    }

    public function init()
    {
        $this->addElement('text', 'name', array(
                'label'         =>  'Name',
                'attribs'       =>  array('size'=>'10'),
                'validators'    =>  array(
                    (array('validator'  =>  new Zend_Validate_StringLength(2, 30))),
                ),
                'required'      =>  true,
            )
        );
        
        $this->addElement('text', 'description', array(
                'label'         =>  'Description',
                'attribs'       =>  array('size'=>'20'),
                'validators'    =>  array(
                    (array('validator'  =>  new Zend_Validate_StringLength(2, 30))),
                ),
                'required'      =>  false,
            )
        );


        $percentage = new Zend_Form_Element_Text('percentage');
        $percentage->setLabel('Tax Percentage')
                    ->setRequired(true)
                    #->addValidator(new Zend_Validate_Float)
                    ->setAttrib('size', '5');      
        $this->addElement($percentage);
        
        $this->addElement('submit', 'submit', array (
                        'class' => 'submit_button'
                        )
        );
        
        $this->getElement('submit')->setLabel('Edit tax type');
        $taxType = new Core_Model_Tax_Type($this->_taxTypeId);    
        $this->populate((array) $taxType->fetch());
    }    
}


