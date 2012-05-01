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
 */

/*
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class BV_View_Helper_UrlWithGetParams extends Zend_View_Helper_Url
{

    /**
     * Generates an url given the name of a route.
     *
     * @access public
     *
     * @param  array $urlOptions Options passed to the assemble method of the Route object.
     * @param  mixed $name The name of a Route to use. If null it will use the current Route
     * @param  bool $reset Whether or not to reset the route defaults with those provided
     * @param bool $retainGetParams Whether or not to retain the HTTP GET paramaters in the URI
     * @return string Url for the link href attribute.
     */

    public function UrlWithGetParams(array $urlOptions = array(), $name = null, $reset = false, $encode = true, $retainGetParams = true)
    {
        $url =  $this->view->url($urlOptions, $name, $reset, $encode);
        $url .= '?';

        if ($retainGetParams === true) {
            foreach ($_GET as $key=>$value) {
                $url .= urlencode($key) . "=" . urlencode($value) . "&";
            }                                                         
        }
        
        /**
         * Cut off the last & symbol from the string
         */
        $url = rtrim($url, "&");

        return htmlentities($url);
    }
}


