<?php
/*
 *
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
