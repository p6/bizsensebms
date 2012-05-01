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
 * an electronic mail 
 * to info@binaryvibes.co.in
 * 
 * Or write paper mail to
 * 
 * #506, 10th B Main Road,
 * 1st Block, Jayanagar,
 * Bangalore - 560 011
 *
 * LICENSE: GNU GPL V3
 *
 * @category    BizSense
 * @package     Core
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt. Ltd.
 * @version    $Id:$
 */

/**
 *
 * @category    BizSense
 * @package     Core
 * @subpackage  Core_Model
 * @copyright   Copyright (c) 2008 Binary Vibes Information Technologies 
 * Pvt Ltd
 */
class Core_Form_Lead_Element_AssignedTo
{
    public function getElement()
    {
        $assignedTo = new Zend_Dojo_Form_Element_FilteringSelect('assigned_to');
        $assignedTo->setLabel('Assign Lead To')
            ->setAutoComplete(true)
            ->setStoreId('leadAssignedToStore')
            ->setStoreType('dojo.data.ItemFileReadStore')
            ->setStoreParams(array('url'=>'/lead/assignto'))
            ->setAttrib("searchAttr", "email")
            ->setRequired(false);
        return $assignedTo;
    }
}
