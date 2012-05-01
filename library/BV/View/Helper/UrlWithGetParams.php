<?php
/** Copyright (c) 2010, Sudheera Satyanarayana - http://techchorus.net, 
     Binary Vibes Information Technologies Pvt. Ltd. and contributors
 *  All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the names of Sudheera Satyanarayana nor the names of the project
 *     contributors may be used to endorse or promote products derived from this
 *     software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
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


