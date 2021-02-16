<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


class rednao_phone_renderer extends rednao_base_elements_renderer {


    public function GetString($formElement,$entry)
    {
        return htmlspecialchars($entry["area"].'-'.$entry["phone"]);
    }

	public function GetExValues($formElement, $entry)
	{
		return array(
			"exvalue1"=> htmlspecialchars($entry["area"]),
			"exvalue2"=> htmlspecialchars($entry["phone"]),
            "exvalue3"=>"",
            "exvalue4"=>"",
            "exvalue5"=>"",
            "exvalue6"=>""
		);
	}
}