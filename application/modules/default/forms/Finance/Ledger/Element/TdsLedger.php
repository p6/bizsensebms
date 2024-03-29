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

class Core_Form_Finance_Ledger_Element_TdsLedger
{
    protected $_name = 'tds_ledger_id';
    
    protected $_label = 'TDS ledger';
    
    /**
     * @param string name the element name 
     * @return fluent interface
     */
    public function setName($name = '')
    {
        if (isset($name) and is_string($name)) {
            $this->_name = $name;
        } 

        return $this;
    }
    
    /**
     * @param string name the element label 
     * @return fluent interface
     */
    public function setLabel($label = '')
    {
        if (isset($label) and is_string($label)) {
            $this->_label = $label;
        } 

        return $this;
    }
    
    /**
     * @return object Zend_Dojo_Form_Element_FilteringSelect
     *  The drop down branch select form element account id
     */
    public function getElement()
    {
        $element = new Zend_Dojo_Form_Element_FilteringSelect($this->_name);
        $element->setLabel($this->_label)
                ->setAutoComplete(true)
                ->setStoreId('tdsStore')
                ->setStoreType('dojo.data.ItemFileReadStore')
                ->setStoreParams(array('url'=>'/finance/ledger/tdsstore'))
                ->setAttrib("searchAttr", "name")
                ->setRequired(false);
       return $element;
    }
    
    
}


