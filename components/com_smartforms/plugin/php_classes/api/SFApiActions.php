<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/**
 * Created by PhpStorm.
 * User: Edgar
 * Date: 9/11/2016
 * Time: 8:17 AM
 */
class SFApiActions
{
    public function register_hooks()
    {
        SmartFormsActionManager::GetInstance()->AddAction('smart_formsa_include_systemjs',$this,'include_systemjs');
    }

    public function include_systemjs(){
        SmartFormsFilterManager::GetInstance()->ApplyFilter('smart_formsf_include_systemjs','');
    }
}