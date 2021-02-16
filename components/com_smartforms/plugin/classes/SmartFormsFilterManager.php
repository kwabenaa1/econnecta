<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class SmartFormsFilterManager{
    private static $filterManager;
    public $filters=array();

    /**
     * @return SmartFormsFilterManager
     */
    public static function GetInstance(){
        if(SmartFormsFilterManager::$filterManager==null)
        {
            SmartFormsFilterManager::$filterManager=new SmartFormsFilterManager();
        }

        return SmartFormsFilterManager::$filterManager;
    }



    public function AddFilter($name,$instance,$methodName)
    {
        if(!isset($this->filters[$name]))
        {
            $this->filters[$name]=array();
        }

        $this->filters[$name][]=array('instance'=>$instance,'methodName'=>$methodName);
    }

    public function ApplyFilter($name,$args){
        if(!isset($this->filters[$name]))
        {
            return $args;
        }

        foreach ($this->filters[$name] as $filter)
        {
            $arguments=func_get_args();
            array_shift($arguments);
            if($arguments<=2)
            {
                $args=$filter['instance']->{$filter['methodName']}($args);
            }else{

                $args=call_user_func_array(array($filter['instance'],$filter['methodName']), $arguments);
            }


        }

        return $args;

    }


}