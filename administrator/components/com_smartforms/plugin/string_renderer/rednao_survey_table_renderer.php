<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class rednao_survey_table_renderer extends rednao_base_elements_renderer {


    public function GetString($formElement,$entry)
    {
        $table='<table style="border-color:#ddd;Border-style:solid;border-width:1px;" border="1">';
        foreach($entry['values'] as $item=>$value)
        {
            $table.="<tr><td style='padding:5px;'><strong>".htmlspecialchars($value['QuestionLabel'])."</strong></td><td>".htmlspecialchars($value['ValueLabel'])."</td></tr>";
        }
        $table.='</table>';
        return $table;
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