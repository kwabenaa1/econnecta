<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class SmartFormsAdditionalFieldsList{
    public function __construct()
    {
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaoimagepicker',$this, 'smart_forms_af_rednaoimagepicker' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaosignature',$this, 'smart_forms_af_rednaosignature' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaodatatable',$this, 'smart_forms_af_rednaodatatable' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaotermofservice',$this, 'smart_forms_af_rednaotermofservice' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaoimage',$this, 'smart_forms_af_rednaoimage' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaogrouppanel',$this, 'smart_forms_af_rednaogrouppanel' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaorepeater',$this, 'smart_forms_af_rednaorepeater' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaotimepicker',$this, 'smart_forms_af_rednaotimepicker' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaorecaptcha2',$this, 'smart_forms_af_rednaorecaptcha2' );
        SmartFormsActionManager::GetInstance()->AddAction( 'smart_forms_af_rednaocurrency',$this, 'smart_forms_af_rednaocurrency' );
        SmartFormsFilterManager::GetInstance()->AddFilter( 'smart_forms_af_names',$this, 'smart_forms_af_names' );
    }

    public function smart_forms_af_rednaocurrency(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaocurrency.js');
    }

    public function smart_forms_af_rednaoimagepicker(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaoimagepicker.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/imagePicker/image-picker.min.js');
        JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/imagePicker/image-picker.css');
    }

    public function smart_forms_af_rednaosignature(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/signature/jSignature.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaosignature.js');

    }

    public function smart_forms_af_rednaodatatable(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaotable/handsontable.full.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaotable/rednaodatatable.js');
        JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaotable/handsontable.full.min.css');

    }


    public function smart_forms_af_rednaoimage(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaoimage.js');
    }

    public function smart_forms_af_rednaogrouppanel(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaogrouppanel.js');
    }

    public function smart_forms_af_rednaorepeater(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaorepeater.js');
    }

    public function smart_forms_af_rednaotimepicker(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/timepicker/timepickerlib.min.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaotimepicker/rednaotimepicker.js');
        JFactory::getDocument()->addStyleSheet( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/timepicker/bootstrap-timepicker.css');
    }

    public function smart_forms_af_rednaorecaptcha2(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaorecaptcha2.js');

    }

    public function smart_forms_af_rednaotermofservice(){
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/formBuilder/additionalFields/rednaotermofservice.js');
    }


    public function smart_forms_af_names($val)
    {
        array_push($val,array('id'=>'rednaoimagepicker','section'=>'Multiple'));
        array_push($val,array('id'=>'rednaosignature','section'=>'Others'));
        array_push($val,array('id'=>'rednaoimage','section'=>'Layout'));
        array_push($val,array('id'=>'rednaogrouppanel','section'=>'Layout'));
        array_push($val,array('id'=>'rednaorepeater','section'=>'Layout'));
        array_push($val,array('id'=>'rednaotimepicker','section'=>'Basic'));
        array_push($val,array('id'=>'rednaocurrency','section'=>'Basic'));
        array_push($val,array('id'=>'rednaorecaptcha2','section'=>'Advanced'));
        array_push($val,array('id'=>'rednaotermofservice','section'=>'Advanced'));
        //array_push($val,array('id'=>'rednaodatatable','section'=>'Others'));
        return $val;
    }


}

$SmartFormsAdditionalFieldListVar=new SmartFormsAdditionalFieldsList();
