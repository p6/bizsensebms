<?php
/*
 * Class overview in a line or two
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
class Core_Form_Product_Category_Create extends Zend_Form
{

    public function init()
    {
        $name = $this->createElement('text', 'name')
                                ->setRequired(true)
                                ->addValidator(new Zend_Validate_Db_NoRecordExists('product_category', 'name'))
                                ->setLabel('Name');

        $description = $this->createElement('text', 'description')
                                ->setLabel('Description');

        $submit = $this->createElement('submit', 'submit');

        $this->addElements(array($name, $description, $submit)); 
    }
}    
