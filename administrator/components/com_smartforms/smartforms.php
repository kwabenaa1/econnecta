<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );





require_once (JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsConfig.php');
require_once (SMART_FORMS_PUBLIC_DIR.'SmartFormBuilder.php');

require_once (SMART_FORMS_PUBLIC_DIR.'php_classes/api/SFApiFilters.php');
require_once (SMART_FORMS_PUBLIC_DIR.'php_classes/api/SFApiActions.php');
SmartFormBuilder::Initialize();
$apiFilters=new SFApiFilters();
$apiFilters->register_hooks();

$apiActions=new SFApiActions();
$apiActions->register_hooks();

jimport('joomla.version');
$version = new JVersion();

$task=JFactory::getApplication()->input->get('task','','raw');
if($task=='execute_ajax')
{
    require_once SMART_FORMS_PUBLIC_DIR.'smart-forms-ajax.php';
    $ajax=new SmartFormsAjax();
    $ajax->ProcessRequest();
    return;
}

if($task=='list')
{
    require_once SMART_FORMS_DIR.'main_screens/smart-forms-select-form.php';
    return;
}


$action =JFactory::getApplication()->input->get('act','','raw');
if($action=='')
    $action="manageforms";

$title='';
switch ($action)
{
    case "import":
        require_once (SMART_FORMS_PUBLIC_DIR.'smart-forms-db-helper.php');

        $file=JFactory::getApplication()->input->files->get('file_uploaded');
        if($file!=null)
        {
            $content=file_get_contents($file['tmp_name']);
            $content=json_decode($content);

            if($content!=null)
            {
                $dbResult=SmartFormsDBHelper::Insert('#__smartforms',array('form_name'=>$content->form_name,
                    'element_options'=>$content->element_options,
                    'form_options'=>$content->form_options,
                    'client_form_options'=>$content->client_form_options,
                    'donation_email'=>$content->donation_email
                ));
            }
        }

        $title='Manage Forms';
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-list.php';
        break;
    case "exportform":

        

        if(isset($_GET['formid']))
        {
            $formId = $_GET["formid"];

            $db = JFactory::getDBO();
            $db->setQuery("SELECT * FROM #__smartforms where form_id= ".$db->quote($formId));
            $row = $db->loadAssoc();
            if($row==null)
            {
                echo "Form does not exists!, please select another valid form";
                return;
            }

            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename='.$row['form_name'].'.export');
            echo json_encode($row);
            die();
        }
        





        $title='Manage Forms';
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-list.php';
        break;
    case "manageforms":
        $title='Manage Forms';
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-list.php';
        break;
    case "deleteform":
        $title='Manage Forms';
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-list.php';
        break;
    case "createform":
        $title='Create Form';
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-add-new.php';
        break;
    case "entries":
        $title='Entries';
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-entries.php';
        break;
    case "export":
        require_once SMART_FORMS_DIR.'smart-forms-exporter.php';
        break;
    case "support":
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-wishlist.php';
        break;
    case "tutorials":
        require_once SMART_FORMS_DIR.'main_screens/smart-forms-tutorials.php';
        break;
}
$app = JFactory::getApplication();
$app->JComponentTitle = "<h1 class=\"page-title\">SmartForms: $title</h1>";