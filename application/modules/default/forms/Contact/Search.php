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
 * Bangalore â€“ 560 011
 *
 * LICENSE: GNU GPL V3
 */

/**
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies 
 * Pvt. Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Form_Contact_Search
{
    public function getForm()
    {

        $form = new Zend_Form;
        $form->setAction('/contact');
        $form->setMethod('get');
        $form->setName('search');

        $name = $form->createElement('text', 'name');
        $name->setLabel('Name');

        $city = $form->createElement('text', 'city');
        $city->setLabel('City');

        $assignedTo = new Zend_Dojo_Form_Element_FilteringSelect('assignedTo');
        $assignedTo->setLabel('Assigned to')
            ->setAutoComplete(true)
            ->setStoreId('accountStore')
            ->setStoreType('dojo.data.ItemFileReadStore')
            ->setStoreParams(array('url'=>'/user/jsonstore'))
            ->setAttrib("searchAttr", "email")
            ->setRequired(false);

        $branch = new Core_Form_Branch_Element_Branch;
        $branchId = $branch->getElement();
        
        $submit = $form->createElement('submit', 'submit')
                        ->setAttrib("class", "submit_button")
                        ->setLabel('Search');

        $form->addElements(array($name, $city, $assignedTo, $branchId, $submit));

        return $form;

    }

}

