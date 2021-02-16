<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class plgAjaxPublicAjax extends JPlugin
{
    function onAjaxPublicAjax()
    {
        if(JFactory::getApplication()->input->get('action','','raw')=='')
        {
            return '';
        }

        $action= JFactory::getApplication()->input->get('action','','raw');

        switch ($action){
            case 'rednao_smart_forms_save_form_values':
                require_once (JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsConfig.php');
                require_once(SMART_FORMS_PUBLIC_DIR.'smart-forms-ajax.php');
                $ajax=new SmartFormsAjax();
                return $ajax->rednao_smart_forms_save_form_values();
                break;
            case 'SubmittingFormWithFiles':
                $data="";
                if( JFactory::getApplication()->input->get("data",'','raw')!=''){
                    $data= JFactory::getApplication()->input->get("data",'','raw');
                }

                $data=json_decode($data,true);
                foreach($data as $key=>$value)
                {
                    if($key=='captcha')
                    {
                        JFactory::getApplication()->input->set($key,$value);
                    }
                    else
                    {
                        JFactory::getApplication()->input->set($key,$value);
                    }

                }



                require_once (JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsConfig.php');
                require_once(SMART_FORMS_PUBLIC_DIR.'smart-forms-ajax.php');
                $ajax=new SmartFormsAjax();
                return $ajax->rednao_smart_forms_save_form_values();
                break;
            case 'openfile':
                $id= JFactory::getApplication()->input->get('id','','raw');

                require_once (JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsConfig.php');
                $db=JFactory::getDBO();
                $db->setQuery("select original_name,file_name,file_mime from #__smart_forms_uploaded_files where file_key=".$db->quote($id));
                $result=$db->loadAssoc();
                if($result==null||count($result)==0)
                {
                    echo 'File not found';
                    die();
                }
                header('Content-type: '.$result['file_mime']);
                header('Content-Disposition: inline; filename="'.$result['original_name'].'"');
                readfile($result['file_name']);
                die();
                break;

        }

        return '';

    }



}
