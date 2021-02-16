<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class SmartFormsOptionManager{
    private static $optionManager;

    /**
     * @return SmartFormsOptionManager
     */
    public static function GetInstance(){
        if(SmartFormsOptionManager::$optionManager==null)
        {
            SmartFormsOptionManager::$optionManager=new SmartFormsOptionManager();
        }

        return SmartFormsOptionManager::$optionManager;
    }

    public function GetOption($optionName,$defaultValue='')
    {
        $db=JFactory::getDBO();
        $db->setQuery("select option_value from #__smart_forms_options where option_name=".$db->quote($optionName));
        $result=$db->loadResult();
        if($result===null)
        {
            return $defaultValue;
        }

        return $result;

    }

    public function UpdateOption($optionName,$value)
    {
        $db=JFactory::getDBO();
        $db->setQuery("select true from #__smart_forms_options where option_name=".$db->quote($optionName));
        $result=$db->loadResult();
        if($result===null)
        {
            $db->setQuery('insert into #__smart_forms_options (option_name,option_value) values('.$db->quote($optionName).','.$db->quote($value).')');
            $db->execute();
        }else{
            $db->setQuery('update #__smart_forms_options set option_value='.$db->quote($value).' where option_name='.$db->quote($optionName));
            $db->execute();
        }


    }

}