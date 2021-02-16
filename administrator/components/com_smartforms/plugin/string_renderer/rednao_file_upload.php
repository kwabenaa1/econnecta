<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class rednao_file_upload extends  rednao_base_elements_renderer{

    public function GetString($formElement,$entry)
    {
		$html="<p>";
		$firstElement=true;
		foreach($entry as $value)
		{
			if(!isset($value["path"]))
				continue;
			if($firstElement)
				$firstElement=false;
			else
				$html.="<br/>";
			$html.='<a href="'.$value["path"].'">'.$value["path"].'</a>';
		}

		$html.="</p>";
		return $html;
    }

	public function GetExValues($formElement, $entry)
	{
		$html="";
		$firstElement=true;
		$value=array();
		$value['path']='';
		$value['ppath']='';
		foreach($entry as $value)
		{
			if($firstElement)
				$firstElement=false;


			$html.= $value["path"].';;;';
		}

		return array(
			"exvalue1"=>htmlspecialchars($html),
            "exvalue2"=>$value["path"],
            "exvalue3"=>$value["ppath"],
            "exvalue4"=>"",
            "exvalue5"=>"",
            "exvalue6"=>""
		);
	}
}