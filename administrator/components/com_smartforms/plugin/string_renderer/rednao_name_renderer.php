<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );


class rednao_name_renderer extends rednao_base_elements_renderer {


    public function GetString($formElement,$entry)
    {
        return htmlspecialchars($entry["firstName"].' '.$entry["lastName"]);
    }

	public function GetExValues($formElement, $entry)
	{
		return array(
			"exvalue1"=>htmlspecialchars($entry["firstName"]),
			"exvalue2"=>htmlspecialchars($entry["lastName"]),
            "exvalue3"=>"",
            "exvalue4"=>"",
            "exvalue5"=>"",
            "exvalue6"=>""
		);
	}
}