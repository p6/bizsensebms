<?php
/*
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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt.
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

/**
 * Edit Organization details
 */
class Core_Form_Org_Edit extends Zend_Form
{
    
    public function init()
    {
        $db = Zend_Registry::get('db');

        $org = $db->fetchRow("SELECT * FROM organization_details", null, Zend_Db::FETCH_ASSOC);

        $this->setAction('/admin/org/edit');
        $this->setMethod('post');


        $companyName = $this->createElement('text', 'company_name')
                            ->setLabel('Company Name')
                            ->addValidator(new Zend_Validate_StringLength(0, 150))
                            ->setRequired(true);

        $website = $this->createElement('text', 'website')
                        ->setLabel('URL')
                        ->setDescription('The organization\'s website address, for example, http://example.com')
                        ->addValidator(new Zend_Validate_StringLength(0, 200))
                        ->addValidator(new BV_Validate_Uri());

        $description = $this->createElement('textarea', 'description')
                            ->setLabel('Description')
                            ->addValidator(new Zend_Validate_StringLength(0, 500))
                            ->setAttribs(array(
                                'rows' => 5,
                                'cols' => 80
                            ));
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button');

      
        $this->addElements(array($companyName, $website, $description, $submit));
        $this->populate($org);    

        new BV_Filter_AddStripTagToElements($this);
        return $this;

    }
}

