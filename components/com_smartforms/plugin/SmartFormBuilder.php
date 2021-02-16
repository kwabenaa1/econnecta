<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class SmartFormBuilder{
    /**
     * @var SmartFormBuilder
     */
    private static $instance;
    public static function Initialize(){
        if(SmartFormBuilder::$instance==null)
        {
            SmartFormBuilder::$instance = new SmartFormBuilder();
        }
        SmartFormBuilder::$instance->CreateHooks();
        require_once SMART_FORMS_PUBLIC_DIR.'pr/smart-forms-pr.php';
    }

    private function CreateHooks(){
        SmartFormsFilterManager::GetInstance()->AddFilter('smart_forms_add_form_elements_dependencies',$this,'AddDependencies');
    }

    public function AddDependencies(){

        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/bundle/datastores_bundle.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/polyfill.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/es6-promise/es6-promise.min.js');
        $dependencies[]='smart-forms-data-store';
        $dependencies[]='smart-forms-promise';
        return $dependencies;
    }
}