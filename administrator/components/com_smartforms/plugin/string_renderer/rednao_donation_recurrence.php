<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class rednao_donation_recurrence extends  rednao_base_elements_renderer{

	public function GetString($formElement,$entry)
	{
		switch($entry["value"])
		{
			case 'OT':
				return 'One time';
			case 'D':
				return 'Daily';
			case 'W':
				return 'Weekly';
			case 'M':
				return 'Monthly';
			case 'Y':
				return 'Yearly';
		}

		return '';

	}

	public function GetExValues($formElement, $entry)
	{
		return array(
			"exvalue1"=>$this->GetString($formElement,$entry),
            "exvalue2"=>"",
            "exvalue3"=>"",
            "exvalue4"=>"",
            "exvalue5"=>"",
            "exvalue6"=>""
		);
	}
}