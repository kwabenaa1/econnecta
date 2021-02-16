<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
require_once (JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsConfig.php');

class SmartFormsLoader
{
    public function LoadForm($form_id)
    {
        require_once(SMART_FORMS_PUBLIC_DIR . 'additional_fields/smart-forms-additional-fields-list.php');
        //$options=get_transient("rednao_smart_forms_$form_id");
        $options = false;
        if ($options == false)
        {

            /** @noinspection PhpUndefinedMethodInspection */
            $result = "";
            if ($form_id != -1)
            {
                $db=JFactory::getDBO();
                $db->setQuery("select form_id,element_options,form_options,client_form_options from #__smartforms where form_id=".$db->quote($form_id));
                $result=$db->loadAssocList();
                if (count($result) > 0)
                {
                    $result = $result[0];
                    $options = array('elements' => $result['element_options'],
                        'options' => $result['form_options'],
                        'form_id' => $result['form_id'],
                        'client_form_options' => $result['client_form_options']
                    );


                }
            } else
                $options = array('options'=>'');

        }


        $additionalFields = array();
        if (JFactory::getApplication()->input->get('task','','raw')=='preview')
            $additionalFields = SmartFormsFilterManager::GetInstance()->ApplyFilter('smart_forms_af_names', $additionalFields);
        else
        {
            $formOptions = json_decode($options['options'], true);
            if (isset($formOptions["AdditionalFields"]))
            {
                foreach ($formOptions["AdditionalFields"] as $field)
                {
                    array_push($additionalFields, array("id" => $field));
                }
            }
        }

        $generatorDependencies = array('isolated-slider', 'smart-forms-event-manager', 'smart-forms-generator-interface', 'smart-forms-form-elements');

        require_once(SMART_FORMS_PUBLIC_DIR.'smart-forms-bootstrap.php');

        $formElementDependencies = array('isolated-slider', 'smart-forms-form-elements-container');
        require_once (SMART_FORMS_PUBLIC_DIR.'SmartFormBuilder.php');
        SmartFormBuilder::Initialize();
        $formElementDependencies = SmartFormsFilterManager::GetInstance()->ApplyFilter('smart_forms_add_form_elements_dependencies', $formElementDependencies);



        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/eventmanager.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/subscriber_interfaces/ismart-forms-generator.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/form-generator.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/container/Container.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formelements.js');
        foreach ($additionalFields as $field)
        {
            array_push($generatorDependencies, 'smart-forms-af-' . $field['id']);
            SmartFormsActionManager::GetInstance()->DoAction('smart_forms_af_' . $field['id']);
        }
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formula/formula.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/formula/formulamanager.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/properties/manipulators.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bundle/conditionalHandlers_bundle.js');

        if ($form_id == -1)
            JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/previewHelper.js');

        SmartFormsActionManager::GetInstance()->DoAction('smart_forms_pr_add_form_elements_extensions');



        JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/smartFormsSlider/jquery-ui-1.10.2.custom.min.css');
        JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/css/formBuilder/custom.css');


        $random = rand();
        if ($form_id == -1)
            $random = "sfpreviewcontainer";

        if (!defined("SMART_DONATIONS_PLUGIN_URL"))
            define("SMART_DONATIONS_PLUGIN_URL", "");

        if (!defined("SMART_DONATIONS_SANDBOX"))
            define("SMART_DONATIONS_SANDBOX", "");


        $user = JFactory::getUser();
        $userName = '';
        $firstName = '';
        $lastName = '';
        $email = '';
        if ($user->id!==null)
        {
            $userName=$user->username;
            $email=$user->email;
            $firstName=$user->name;
        }




            if ($options == null)
                return "";
            $loadedForm = "";
            if ($form_id != -1)
                $loadedForm = "window.smartFormsItemsToLoad.push({ 'form_id':" . $options['form_id'] . ",  'elements':" . $options['elements'] . ",'client_form_options':" . $options['client_form_options'] . ",'container':'formContainer$random'});";
            $container="<div id='formContainer$random' class='sfForm rednaoFormContainer SfFormElementContainer bootstrap-wrapper'></div>";
            JFactory::getDocument()->addScriptDeclaration(
                "var sfbaseurl = '".JURI::base()."';".
             "var ajaxurl = 'index.php?option=com_ajax&plugin=publicajax&format=raw';".
              "var smartFormsCurrentTime=new Date('" . date('D M d Y H:i:s O') . "');".
                "var SmartFormsElementsTranslation={};".
            	"var smartFormsUserName=\"" . $userName . "\";".
            	"var smartFormsFirstName=\"" . $firstName . "\";".
            	"var smartFormsLastName=\"" . $lastName . "\";".
            	"var smartFormsEmail=\"" . $email . "\";".
                "var smartFormsPath=\"" . JUri::root( false ) . '/components/com_smartforms/plugin/' . "\";".
                "var smartDonationsRootPath=\"" . SMART_DONATIONS_PLUGIN_URL . "\";".
                "var smartDonationsSandbox=\"" . SMART_DONATIONS_SANDBOX . "\";".
                "var smartFormsAdditionalFields" . (!isset( $options['form_id'])|| $options['form_id'] == null ? 0 : $options['form_id']) . "=" . json_encode($additionalFields) . ";".
                "var smartFormsDesignMode=false;".
                "if(!window.smartFormsItemsToLoad)".
                    "window.smartFormsItemsToLoad=new Array();".
                $loadedForm);


        return $container;
    }


}