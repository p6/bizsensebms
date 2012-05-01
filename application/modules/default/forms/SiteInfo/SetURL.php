<?php
/*
 * Site site footer
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
class Core_Form_SiteInfo_SetURL extends Zend_Form
{
    public function init()
    {
        $siteURL = $this->createElement('text', 'site_url')
                        ->setLabel('Website URL')
                        ->addValidator(new Zend_Validate_StringLength(0, 200))
                        ->addValidator(new BV_Validate_Uri())
                        ->setValue('');
        
        $submit = $this->createElement('submit', 'submit')
                       ->setAttrib('class', 'submit_button')
                        ->setLabel('Submit'); 
        $this->addElements(array($siteURL, $submit));

    }
}


