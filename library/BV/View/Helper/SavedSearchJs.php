<?php
/**
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

/**
 * LICENSE: GNU GPL V3
 *
 * @description Binary Vibes BizSense - web based CRM and ERP software
 * @category   BizSense
 * @package    Core
 * @copyright  Copyright (c) 2008 Binary Vibes Information Technologies Pvt. 
 * Ltd. (http://binaryvibes.co.in)
 * @version    $Id:$
 */
class BV_View_Helper_SavedSearchJs extends Zend_View_Helper_Url
{
    /**
     * @param int 
     * @return string javascript code
     */
    public function savedSearchJs($type)
    {
        $script = <<<EOD
<div dojoType="dijit.form.DropDownButton">
    <span>
        Save this search
    </span>
    <div dojoType="dijit.TooltipDialog" id="formDialog">
        <label for="name">
            Name:
        </label>
        <input dojoType="dijit.form.TextBox" id="search_name" name="search_name">
        <br>
        <button dojoType="dijit.form.Button" type="submit" id="search_submit" onclick="saveSearchHandle('$type')">
            Save
        </button>
    </div>
</div>
EOD;
        return $script;
    }
}
