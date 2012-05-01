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
 * @copyright  Copyright (c) 2010 Binary Vibes Information Technologies Pvt. 
 *  Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class Core_Model_Org extends Core_Model_Abstract
{
    
    /**
     * @see Core_Model_Abstract::$_dbTableClass
     */
    protected $_dbTableClass = 'Core_Model_DbTable_OrganizationDetails';

    protected $_record;

    /**
     * @return array the organization details
     */
    public function fetch()
    {
        $table = $this->getTable();
        $select = $table->select()->where('organization_id = 1');


        $cache = $this->getCache();
        if (!$result = $cache->load('Core_Model_Org')) {
            $result = $table->fetchRow($select);
            $cache->save($result, 'Core_Model_Org');
        }
        return $result->toArray();
    }

    /**
     * Update the organization details
     * @param array $data the values to be updated
     */
    public function edit($data = array())
    {
        $table = $this->getTable();
        $data = $this->unsetNonTableFields($data);
        $where = $table->getAdapter()->quoteInto('organization_id = ?', 1);
        $table->update($data, $where);
    }

    /**
     * @return string organization name
     */
    public function getName()
    {
        $organizationData = $this->fetch();
        return $organizationData['company_name'];
    }

    public function getWebsiteUrl()
    {
        $organizationData = $this->fetch();
        return $organizationData['website'];
    }
}


