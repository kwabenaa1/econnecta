<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
/**
 * Created by PhpStorm.
 * User: edseventeen
 * Date: 11/28/13
 * Time: 8:57 PM
 */

class rednao_checkbox_input_renderer extends  rednao_base_elements_renderer{
    public function GetString($formElement,$entry)
    {
        return ($entry["checked"]=="Yes"?__("Yes"):__("No")).". ".htmlspecialchars($entry["value"]);
    }

	public function GetExValues($formElement, $entry)
	{
		return array(
			"exvalue1"=>htmlspecialchars($entry["value"]),
			"exvalue2"=>htmlspecialchars($entry["checked"]),
            "exvalue3"=>"",
            "exvalue4"=>"",
            "exvalue5"=>"",
            "exvalue6"=>""
		);
	}
}