<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class SmartFormsAjax{
    public function __construct()
    {
        defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );



    }

    public function ProcessRequest(){
        $this->CheckIfAdmin();
        $action=JFactory::getApplication()->input->get('action','','raw');
        if($action=='')
            return;
        if($action=='rednao_smart_forms_save')
        {
            $this->rednao_smart_forms_save();
            return;
        }

        if($action=='smart_forms_skip_tutorial')
        {
            $this->smart_forms_skip_tutorial();
            return;
        }

        if($action=='rednao_smart_forms_entries_list')
        {
            $this->rednao_smart_forms_entries_list();
            return;
        }

        if($action=='rednao_smart_forms_execute_op')
        {
            $this->rednao_smart_forms_execute_op();
            return;
        }

        if($action=='rednao_smart_forms_edit_form_values')
        {
            $this->rednao_smart_forms_edit_form_values();
            return;
        }

        if($action=='rednao_smart_form_send_test_email')
        {
            $this->rednao_smart_form_send_test_email();
            return;
        }


        SmartFormsActionManager::GetInstance()->DoAction('ajax_'.$action);
        throw new Exception('Invalid ajax request '.'ajax_'.$action);
        
        
        
    }

    function smart_forms_skip_tutorial(){

        SmartFormsOptionManager::GetInstance()->UpdateOption('smart_forms_show_tutorial','y');
    }

    public function CheckIfAdmin(){
        if ( !JFactory::getUser()->authorise('core.manage', 'com_smartforms'))
        {
            return JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'));
            die();
        }
    }


    function GetPostValue($parameterName)
    {



        return JFactory::getApplication()->input->get($parameterName,'','raw');
    }

    function smart_forms_export(){
        require_once 'smart-forms-exporter.php';
    }





    function rednao_smart_forms_save()
    {
        $this->CheckIfAdmin();
        $form_id=$this->GetPostValue("id");
        $element_options=$this->GetPostValue("element_options");
        $form_options=$this->GetPostValue("form_options");
        $client_form_options=$this->GetPostValue("client_form_options");
        $donation_email=$this->GetPostValue("donation_email");

        //$form_options=str_replace("\\\"","\"",$form_options);
        $formParsedValues=json_decode($form_options);

        $db=JFactory::getDBO();
        require_once (SMART_FORMS_PUBLIC_DIR.'smart-forms-db-helper.php');
        if($formParsedValues->Name=="")
        {
            $message="Name is mandatory";
        }else{
            if($form_id=="0")
            {
                $db->setQuery("SELECT count(*) FROM #__smartforms where form_name=".$db->quote($formParsedValues->Name));
                $count=$db->loadResult();

                if($count>0)
                {
                    $message="Form name already exists";

                }else
                {

                    $dbResult=SmartFormsDBHelper::Insert('#__smartforms',array('form_name'=>$formParsedValues->Name,
                        'element_options'=>$element_options,
                        'form_options'=>$form_options,
                        'client_form_options'=>$client_form_options,
                        'donation_email'=>$donation_email
                    ));


                    $form_id=$dbResult->insertid();
                    $message="saved";
                }
            }else
            {
                SmartFormsDBHelper::Update('#__smartforms',
                    array(
                        'form_name'=>$formParsedValues->Name,
                        'element_options'=>$element_options,
                        'form_options'=>$form_options,
                        'client_form_options'=>$client_form_options,
                        'donation_email'=>$donation_email
                    ),array("form_id"=>$form_id));
                $message="saved";


            }

        }


        echo "{\"FormId\":\"$form_id\",\"Message\":\"$message\"}";

        die();
    }


    function rednao_smart_form_short_code_setup()
    {
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
            return;
        }

        global $wpdb;

        $shortCodeOptions=array();
        array_push($shortCodeOptions,array(
            "Name"=>"Forms",
            "ShortCode"=>"sform",
            "Elements"=>array()
        ));

        $result=$wpdb->get_results("SELECT form_id,form_name FROM ".SMART_FORMS_TABLE_NAME);
        //echo "[{\"Id\":\"0\",\"Name\":\"Select a Form\"}";
        foreach($result as $key=>$row)
        {
            array_push($shortCodeOptions[0]["Elements"],array(
                "Id"=>$row->form_id,
                "Name"=>$row->form_name
            ));

        }

        $shortCodeOptions=apply_filters("smart_forms_get_short_code_options",$shortCodeOptions);

        echo json_encode($shortCodeOptions);
        die();
    }


    function rednao_smart_forms_save_form_values()
    {
        include_once(SMART_FORMS_DIR.'php_classes/save/php_entry_saver_base.php');

        $form_id=$this->GetPostValue("form_id");
        $formString=$this->GetPostValue("formString");
        $captcha="";
        if(JFactory::getApplication()->input->get("captcha",'','raw')!='')
            $captcha=JFactory::getApplication()->input->get("captcha",'','raw');


        $phpEntry=new php_entry_saver_base($form_id,$formString,$captcha);
        return $phpEntry->ProcessEntry();

    }

    function rednao_smart_forms_get_form_element_info()
    {
        $formId=GetPostValue("formId");
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
            return;
        }

        global $wpdb;
        $result=$wpdb->get_var($wpdb->prepare("SELECT element_options FROM ".SMART_FORMS_TABLE_NAME.' where form_id=%d',$formId));

        echo '{"elementsInfo":'.$result.'}';

        die();
    }

    function rednao_smart_forms_get_form_options()
    {
        $formId=GetPostValue("formId");
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
            return;
        }

        global $wpdb;
        $result=$wpdb->get_results($wpdb->prepare("SELECT form_options,element_options FROM ".SMART_FORMS_TABLE_NAME.' where form_id=%d',$formId));

        global $current_user;
        echo '{"formOptions":'.$result[0]->form_options;//.'}';
        echo ',"elementOptions":'.$result[0]->element_options.
            ',"CurrentEmail":"'.$current_user->user_email.'"'.
            '}';
        /*echo json_encode(array(
            "formOptions"=>,
            "element_options"=>$result[0]->element_options
            )
        );*/


        die();
    }

    function rednao_get_fixed_field_value($match,$entryData,$elementOptions,$useTestData)
    {
        require_once(SMART_FORMS_DIR.'filter_listeners/fixed-field-listeners.php');
        $match=str_replace("'","\"",$match);
        $fixedFieldParameters=json_decode($match,true);
        if($fixedFieldParameters==null)
        {

            return '';
        }

        try{
            $value=SmartFormsFilterManager::GetInstance()->ApplyFilter("smart-forms-fixed-field-value-".$fixedFieldParameters["Op"],$fixedFieldParameters,$entryData,$elementOptions,$useTestData);
            if($value==$fixedFieldParameters)
                return "";
            if($value==null)
                $value='';
            return strval($value);
        }catch(Exception $e)
        {

        }

        return "";
    }

    function GetValueByField($stringBuilder,$match,$entryData,$elementOptions,$useTestData)
    {
        if(strpos(trim($match),'{')===0)
        {
            return $this->rednao_get_fixed_field_value($match,$entryData,$elementOptions,$useTestData);
        }

        foreach($entryData as $key=>$value)
        {
            $element=null;
            if($key!=$match)
                continue;

            $element=null;
            foreach($elementOptions as $item)
            {
                if($item["Id"]==$key)
                {
                    $element=$item;
                    break;
                }
            }
            if($element==null)
                continue;

            $value= $stringBuilder->GetStringFromColumn($element,$value);
            if($value==""&&$useTestData)
                $value="sample text";
            return $value;
        }

        if($useTestData)
            return "sample text";
    }

    function rednao_smart_forms_entries_list()
    {
        $startDate=$this->GetPostValue("startDate");
        $endDate=$this->GetPostValue("endDate");
        $formId=$this->GetPostValue("form_id");

        $startDate=date('Y-m-d H:i:s', strtotime($startDate));
        $endDate=date('Y-m-d H:i:s', strtotime($endDate .' +1 day'));

        $db=JFactory::getDBO();
        $query="select concat(year(date),'-',month(date),'-' ,day(date)) date,date,entry_id,data from #__smartforms_entry ".
        "where date between ".$db->quote($startDate)." and ".$db->quote($endDate)." and form_id=".$db->quote($formId);
        $db->setQuery($query);
        $result=$db->loadObjectList();
        $isFirstRecord=true;

        echo '{"entries":[';
        foreach($result as $row)
        {
            if($isFirstRecord)
                $isFirstRecord=false;
            else
                echo ",";

            $data=$row->data;
            if($data===NULL||trim($data)===null)
                $data="{}";

            echo '{"date":"'.$row->date.'","entry_id":"'.$row->entry_id.'","data":'.$data."}";

        }
        echo '],"formOptions":';

        $db->setQuery("select element_options from #__smartforms where form_id=".$db->quote($formId));

        $elementOptions=$db->loadResult();
        echo $elementOptions.'}';


        die();
    }

    function rednao_smart_form_send_test_email()
    {
        $FromEmail=$this->GetPostValue("FromEmail");
        $FromName=$this->GetPostValue("FromName");
        $ToEmail=$this->GetPostValue("ToEmail");
        $EmailSubject=$this->GetPostValue("EmailSubject");
        $EmailText=$this->GetPostValue("EmailText");
        $ReplyTo=$this->GetPostValue("ReplyTo");
        $Bcc=$this->GetPostValue("Bcc");
        $elementOptions=$this->GetPostValue("element_options");
        $pdfs=$this->GetPostValue("PDFS");


        if(!is_array($elementOptions))
            $elementOptions=json_decode($elementOptions,true);


        $valueArray=Array(
            "FromEmail"=>$FromEmail,
            "FromName"=>$FromName,
            "ToEmail"=>$ToEmail,
            "EmailSubject"=>$EmailSubject,
            "EmailText"=>$EmailText,
            "ReplyTo"=>$ReplyTo,
            "PDFS"=>$pdfs,
            "Bcc"=>$Bcc
        );
        $entryData=Array();


        if($EmailText=="")
        {
            echo '{"Message":"'."Email text can't be empty".'"}';
            die();
        }

        include_once(SMART_FORMS_DIR.'php_classes/save/php_entry_saver_base.php');
        $entrySaver=new php_entry_saver_base("","","");
        if($entrySaver->SendFormEmail($valueArray,$entryData,$elementOptions,null,true))
            echo '{"Message":"'."Email sent successfully".'"}';
        else
            echo '{"Message":"'."There was an error sending the email, please check the configuration".'"}';
        die();
    }


    function rednao_smart_forms_submit_license()
    {
        include_once(SMART_FORMS_DIR.'smart-forms-license.php');

        $email=GetPostValue("email");
        $key=GetPostValue("key");

        $license=smart_forms_check_license($email,$key,$error);
        if($license["is_valid"])
        {
            echo '{"IsValid":"y","Message":"'.__("License submitted successfully, thank you!!").'","licenseType":"'.$license["licenseType"].'"}';
        }else
        {
            if($error==null)
            {
                echo '{"IsValid":"n",  "Message":"'.__("Invalid user or license").'"}';
            }else
                echo '{"IsValid":"n","Message":"'.__("An error occurred $error").'"}';
        }

        die();
    }

    function rednao_smart_forms_execute_op()
    {
        $id=JFactory::getApplication()->input->get("TransactionId",'','raw');
        $oper=JFactory::getApplication()->input->get("oper",'','raw');

        if($oper=="del")
        {
            $db=JFactory::getDBO();
            $db->setQuery('delete from #__smartforms_entry where entry_id='.$db->quote($id));
            $db->execute();
            global $wpdb;
            if($db->affectedRows()>0)
                echo '{"success":"1"}';
            else
                echo '{"success":"0","message":"'."Could not delete row".'"}';
            die();
        }

    }

    function rednao_smart_forms_dont_show_again()
    {
        update_option('sf_dont_show_again',true);
    }

    function rednao_smart_forms_send_test()
    {
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
            return;
        }

        require_once SMART_FORMS_DIR.'php_classes/smart_forms_troubleshoot/smart_forms_email.php';
        switch(JFactory::getApplication()->input->get("Id",'','raw'))
        {
            case "basic":
                $smartFormsEmail=new smart_forms_email_troubleshoot_basic();
                break;
            case "custom":
                $smartFormsEmail=new smart_forms_email_troubleshoot_custom_smtp();
                break;
        }
        if($smartFormsEmail->Start())
            echo json_encode(
                array(
                    "Passed"=>'y'
                ));
        else
            echo json_encode(
                array(
                    "Passed"=>'n',
                    "Message"=>$smartFormsEmail->LatestError
                ));
        die();
    }


    function rednao_smart_forms_edit_form_values()
    {
        $entryId=$this->GetPostValue("entryId");
        $entryString=$this->GetPostValue("entryString");
        $elementOptions=$this->GetPostValue("elementOptions");


        include_once(SMART_FORMS_DIR.'php_classes/save/php_entry_editor.php');
        $phpEditor=new php_entry_editor();
        echo json_encode(array('result'=>$phpEditor->execute_editor($entryId,$entryString,$elementOptions)));
        die();
    }

}