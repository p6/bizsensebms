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
 * BV Model list abstract class
 * Provides abstract functionality to generate paginator object
 * And to search and sort on the select object
 */
abstract class BV_Model_Index_Abstract
{

    /**
     * The search criteria
     * Typically submitted via a search form
     * Could contain raw HTTP GET or POST values
     */
     protected $_search;
   
    /**
     * The sort criteria
     * Typically submitted via a URI params
     * Could contain raw HTTP GET value
     */
    protected $_sort;

    /**
     * The model on which we are generating the index
     */
    protected $_model;
    

    /*
     * Set the searh and sort criteria
     */
    public function __construct($search = null, $sort = null)
    {
        $this->_search = $search;
        $this->_sort = $sort;
    }

    public function setModel($model = null)
    {
        $this->_model = $model;
    }

    /**
     * @return Zend_Paginator object
     */
    public function getPaginator()
    {
    }
}

