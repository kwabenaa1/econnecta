<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class php_entry_editor
{
    function execute_editor($entryId,$entryData,$elementOptions)
    {
        $db=JFactory::getDBO();
        $db->setQuery('update #__smartforms_entry set data='.$db->quote($entryData).' where entry_id='.$db->quote($entryId));
        $db->execute();
        if($db->getAffectedRows()<=0)
            return true;

        $entryData=json_decode($entryData,true);

        $entryData["_formid"]=$entryId;
        $result=$this->ParseAndInsertDetail($entryId,$entryData,$this->GetFormElementsDictionary($elementOptions));
        return $result;
    }

    public function GetFormElementsDictionary($elementOptions)
    {
        $elements=json_decode($elementOptions,true);
        $formElementsDictionary =array();
        foreach($elements as $element)
            $formElementsDictionary[$element["Id"]]=$element;

        return $formElementsDictionary;
    }


    function ParseAndInsertDetail($entryId,$entryData,$formElementsDictionary)
    {
        include_once(SMART_FORMS_DIR.'string_renderer/rednao_string_builder.php');
        $stringBuilder=new rednao_string_builder();
        $db=JFactory::getDBO();
        $db->setQuery('delete from #__smart_forms_entry_detail where entry_id='.$db->quote($entryId));
        foreach($entryData as $key=>$unprocessedValue)
        {
            if(!isset($formElementsDictionary[$key]))
                continue;

            $fieldConfiguration=$formElementsDictionary[$key];
            $value=$stringBuilder->GetStringFromColumn($fieldConfiguration,$unprocessedValue);
            $exValue=$stringBuilder->GetExValue($fieldConfiguration,$unprocessedValue);
            $dateValue=$stringBuilder->GetDateValue($fieldConfiguration,$unprocessedValue);
            if($dateValue!=null)
                $dateValue=date('Y-m-d',$dateValue);
            $jsonValue=json_encode($unprocessedValue);
            if(!$this->InsertDetailRecord($entryId,$key,$value,$jsonValue,$exValue,$dateValue))
                return true;
        }

        return true;
    }

    function InsertDetailRecord($entry_id, $fieldId, $value, $jsonValue,$exValue,$dateValue)
    {
        require_once (SMART_FORMS_PUBLIC_DIR.'smart-forms-db-helper.php');
        $arrayToInsert=array_merge(array(
            "entry_id"=>$entry_id,
            "field_id"=>$fieldId,
            "json_value"=>$jsonValue,
            "value"=>$value
        ),$exValue);

        if($dateValue!=null)
            $arrayToInsert["datevalue"]=$dateValue;

        $result=SmartFormsDBHelper::Insert('#__smart_forms_entry_detail',$arrayToInsert);
        return $result->getAffectedRows()<=0;
    }
}