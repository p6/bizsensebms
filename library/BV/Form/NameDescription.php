<?php
/**
 * Form provides name and description fields
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

class BV_Form_NameDescription
{
    /*
     * Returns form with name, description and submit fields
     * Name field is required
     */
    public static function getForm()
    {
        
        $form = new Zend_Form;

        $name = $form->createElement('text', 'name')
                                ->setRequired(true)
                                ->setLabel('Name');

        $description = $form->createElement('text', 'description')
                                ->setLabel('Description');
        $submit = $form->createElement('submit', 'submit');

        $form->addElements(array($name, $description, $submit));
        return $form;

    }
}
