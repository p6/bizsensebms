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
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. \
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */

class Core_Form_Newsletter_DomainBlacklist_Edit extends Core_Form_Newsletter_DomainBlacklist_Create
{
    protected $_domainBlacklistModel;

    public function __construct($domainBlacklistModel)
    {
        $this->_domainBlacklistModel = $domainBlacklistModel;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $defaultValues = $this->_domainBlacklistModel->fetch();
        $name = $this->createElement('text', 'domain')
                                ->setRequired(true)
                                ->addValidator(new Zend_Validate_StringLength(1,200))
                                ->addValidator(new Zend_Validate_Db_NoRecordExists(
                                'domain_blacklist', 'domain',array(
                                'field' => 'domain', 'value' => $defaultValues['domain'] )))
                                ->setLabel('Domain Name');
        $this->addElement($name); 
        
        $this->populate($defaultValues);
    }
}
