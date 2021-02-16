<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

//****************************************************************************************add new extension********************************************************


class SmartFormsPR{
    public function __construct()
    {

        SmartFormsActionManager::GetInstance()->AddAction('smart_forms_pr_add_new_extension',$this,'smart_forms_pr_add_new_extension');
        SmartFormsActionManager::GetInstance()->AddAction('smart_forms_pr_add_form_elements_extensions',$this,'smart_forms_pr_add_form_elements_extensions');
        SmartFormsFilterManager::GetInstance()->AddFilter('smart_forms_pr_add_new_js_extension',$this,'smart_forms_pr_add_new_js_extension');

    }

    public function smart_forms_pr_add_new_extension()
    {
        if(SMART_FORMS_IS_PR=='true')
        {
            JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/pr/js/main_screens/add-new-extensions.js');
        }
        SmartFormsActionManager::GetInstance()->DoAction('smart_forms_pr_add_form_elements_extensions');
    }
    public function smart_forms_pr_add_new_js_extension($val)
    {

        if(SMART_FORMS_IS_PR=='true')
        {
            array_push($val,'smart-forms-pr-add-new-extension');
        }
        return $val;
    }



    function smart_forms_pr_add_form_elements_extensions()
    {
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/pr/js/form_element_extensions/form-element-extensions.js');
        JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/pr/css/main-style.css');
    }



}
new SmartFormsPR();