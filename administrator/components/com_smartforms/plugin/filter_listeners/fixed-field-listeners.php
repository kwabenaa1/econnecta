<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class SmartFormsFixedFieldListener{

    public function __construct()
    {
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-get-email-fixed-field-listener',$this,'smart_forms_email_current_date');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-fixed-field-value-CurrentDate',$this,'smart_forms_get_fixed_fields_CurrentDate');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-get-email-fixed-field-listener',$this,'smart_forms_email_original_url');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-fixed-field-value-OriginalUrl',$this,'smart_forms_get_fixed_fields_OriginalUrl');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-get-email-fixed-field-listener',$this,'smart_forms_email_form_id');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-fixed-field-value-FormId',$this,'smart_forms_get_fixed_fields_FormId');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-get-email-fixed-field-listener',$this,'smart_forms_email_ip');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-fixed-field-value-IP',$this,'smart_forms_get_fixed_fields_IP');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-get-email-fixed-field-listener',$this,'smart_forms_logged_user');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-fixed-field-value-USERNAME',$this,'smart_forms_get_fixed_fields_username');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-get-email-fixed-field-listener',$this,'smart_forms_fieldsummary');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart-forms-fixed-field-value-FIELDSUMMARY',$this,'smart_forms_get_fixed_fields_FIELDSUMMARY');
    }
    public function smart_forms_email_current_date($array)
    {
        array_push($array,
            array(
                "Label"=>"Current Date",
                "Op"=>"CurrentDate",
                "Parameters"=>array(
                    "Format"=>"m/d/y"
                )
            )
        );

        return $array;
    }

    public function smart_forms_get_fixed_fields_CurrentDate($fieldParameters,$formData,$elementOptions,$useTestData)
    {
        return date($fieldParameters["Format"]);
    }

    public function smart_forms_email_original_url($array)
    {
        array_push($array,
            array(
                "Label"=>"Original URL",
                "Op"=>"OriginalUrl",
                "Parameters"=>array(
                )
            )
        );

        return $array;
    }

    public function smart_forms_get_fixed_fields_OriginalUrl($fieldParameters,$formData,$elementOptions,$useTestData)
    {
        return JFactory::getApplication()->input->get('requestUrl','','raw');
    }

    public function smart_forms_email_form_id($array)
    {
        array_push($array,
            array(
                "Label"=>"Form Id",
                "Op"=>"FormId",
                "Parameters"=>array(
                )
            )
        );

        return $array;
    }

    public function smart_forms_get_fixed_fields_FormId($fieldParameters,$formData,$elementOptions,$useTestData)
    {
        if($useTestData)
            return 'test';
        return $formData['_formid'];
    }

    public function smart_forms_email_ip($array)
    {
        array_push($array,
            array(
                "Label"=>"IP",
                "Op"=>"IP",
                "Parameters"=>array(
                )
            )
        );

        return $array;
    }

    public function smart_forms_get_fixed_fields_IP($fieldParameters,$formData,$elementOptions,$useTestData)
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function smart_forms_get_fixed_fields_FIELDSUMMARY($fieldParameters,$formData,$elementOptions,$useTestData)
    {
        include_once(SMART_FORMS_DIR.'string_renderer/rednao_string_builder.php');
        $stringBuilder=new rednao_string_builder();

        $summary="";
        foreach($elementOptions as $option){
            $fieldId=$option["Id"];

            if($useTestData)
                $value='test';
            else{
                if(!isset($formData[$fieldId]))
                    continue;
                $value=$stringBuilder->GetStringFromColumn($option,$formData[$fieldId]);
            }

            if(trim($value)!==""){
                if($option['ClassName']=='rednaorepeater'){
                    $summary .=  $value . "<br/>";
                }else
                {
                    $label='';
                    if(isset($option["Label"]))
                        $label= htmlspecialchars($option["Label"]);
                    $summary .= "<strong>" . $label . ":</strong>" . $value . "<br/>";
                }
            }
        }

        return $summary;
    }

    public function smart_forms_logged_user($array)
    {
        array_push($array,
            array(
                "Label"=>"Username",
                "Op"=>"USERNAME",
                "Parameters"=>array(
                )
            )
        );

        return $array;
    }

    function smart_forms_get_fixed_fields_username($fieldParameters,$formData,$elementOptions,$useTestData)
    {
        $current_user = wp_get_current_user();
        if(!$current_user)
            return '';

        return $current_user->user_login;
    }

    public function smart_forms_fieldsummary($array)
    {
        array_push($array,
            array(
                "Label"=>"Field Summary",
                "Op"=>"FIELDSUMMARY",
                "Parameters"=>array(
                )
            )
        );

        return $array;
    }




}

$SmartFormsFieldListener=new SmartFormsFixedFieldListener();


