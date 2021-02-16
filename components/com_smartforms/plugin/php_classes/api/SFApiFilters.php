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
class SFApiFilters
{
    public function register_hooks(){
        SmartFormsFilterManager::GetInstance()->AddFilter( 'smart_formsf_include_systemjs',$this,'include_systemjs');
    }

    public function include_systemjs($htmlContent){


        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/rxjs/rxjs.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/systemjs/system-polyfill.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/systemjs/system.js');
        JFactory::getDocument()->addScript( JUri::root( true ) . '/components/com_smartforms/plugin/js/utilities/systemjs/systemJsMainConfig.js');
        return $htmlContent;
    }
}