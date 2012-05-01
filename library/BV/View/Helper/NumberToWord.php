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

class BV_View_Helper_NumberToWord extends Zend_View_Helper_Abstract
{

    /**  
     * @param int number range 9 digits
     * @return string input inmber in words
     */
    public function numberToWord($number)
    {
        if (($number < 0) || ($number > 999999999))
        {
            throw new Exception("Number is out of range");
        }
        
        /*
         * crore 
         */
        $croreNumber = floor($number / 10000000);
        $number -= $croreNumber * 10000000;

        /**
         * lakhs  
         */
        $lakhNumber = floor($number / 100000);
        $number -= $lakhNumber * 100000;
 
        /**  
         * Thousands (kilo) 
         */ 
        $kiloNumber = floor($number / 1000);
        $number -= $kiloNumber * 1000;

        /**  
         * Hundreds (hecto) 
         */
        $hectoNumber = floor($number / 100);
        $number -= $hectoNumber * 100;

        /**  
         * Tens (deca) 
         */
        $decaNumber = floor($number / 10);
        /**  
         * Ones (single digit/ Number)
         */
        $onesNumber = $number % 10;
        $result = "";
 
        if ($croreNumber)
        {
            $result .= (empty($result) ? "" : " ") .
                    $this->numberToWord($croreNumber) . " Crore ";
        }

        if ($lakhNumber)
        {
            $result .= (empty($result) ? "" : " ") .
                    $this->numberToWord($lakhNumber) . " Lakh ";
        }

        if ($kiloNumber)
        {
            $result .= (empty($result) ? "" : " ") .
                    $this->numberToWord($kiloNumber) . " Thousand ";
        }

        if ($hectoNumber)
        {
         $result .= (empty($result) ? "" : " ") .
                    $this->numberToWord($hectoNumber) . " Hundred ";
        }

        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six",
                      "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve",
                      "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen",
                      "Eightteen","Nineteen");

        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty",
                      "Seventy", "Eigthy", "Ninety");

         if ($decaNumber || $onesNumber)
        {
            if (!empty($result))
            {
                $result .= " ";
            }

            if ($decaNumber < 2)
            {
                $result .= $ones[$decaNumber * 10 + $onesNumber];
            }
            else
            {
                $result .= $tens[$decaNumber];
                if ($onesNumber)
                {  $result .= " " . $ones[$onesNumber]; }
            }
        }

        if (empty($result))
        {
            $result = "zero";
        }

        return $result;
    }
}
