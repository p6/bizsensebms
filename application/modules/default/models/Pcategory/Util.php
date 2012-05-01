<?php
/*
 * Manufacturer utility model
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
class Core_Model_Pcategory_Util
{
    public $db;
    
    function __construct()
    {
        $db = Zend_Registry::get('db');
    }
    
    public static function getPcategories()
    {
        $db = Zend_Registry::get('db');
        #$select = "SELECT currencyId, name, symbol, description FROM currency";
        $select = $db->select();
        $select->from(array('pc'=>'product_category'),
                    array('product_category_id', 'name', 'description'));
        return $select;
        #return $db->fetchAll($select);i

    } 
}
