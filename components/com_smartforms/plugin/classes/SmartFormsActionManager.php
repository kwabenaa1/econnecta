<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
class SmartFormsActionManager{
    private static $actionManager;
    public $actions=array();
    /**
     * @return SmartFormsActionManager
     */
    public static function GetInstance(){
        if(SmartFormsActionManager::$actionManager==null)
        {
            SmartFormsActionManager::$actionManager=new SmartFormsActionManager();
        }

        return SmartFormsActionManager::$actionManager;
    }

    public function HasAction($name)
    {
        return isset($this->actions[$name]);
    }

    public function AddAction($name,$instance,$methodName)
    {
        if(!isset($this->actions[$name]))
        {
            $this->actions[$name]=array();
        }

        $this->actions[$name][]=array('instance'=>$instance,'methodName'=>$methodName);

    }

    public function DoAction($name,$args=null)
    {
        if(!isset($this->actions[$name]))
        {
            return;
        }

        foreach ($this->actions[$name] as $action)
        {
            $action['instance']->{$action['methodName']}($args);

        }

        return $args;
    }


}