<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if(!defined('SMART_FORMS_DIR_URL')){
    define('SMART_FORMS_DIR_URL', 'components/com_smartforms/plugin/');
}

if(!defined('SMART_FORMS_PUBLIC_DIR_URL')){
    define('SMART_FORMS_PUBLIC_DIR_URL', JUri::root( false ).'components/com_smartforms/plugin/');
}



if(!defined('SMART_FORMS_DIR')){
    define('SMART_FORMS_DIR', JPATH_SITE.'/administrator/components/com_smartforms/plugin/');
}

if(file_exists(SMART_FORMS_DIR.'SmartFormsPr.php'))
{
    define('SMART_FORMS_IS_PR','true');
}else{
    define('SMART_FORMS_IS_PR','false');
}

if(!defined('SMART_FORMS_PUBLIC_DIR')){
    define('SMART_FORMS_PUBLIC_DIR', JPATH_SITE.'/components/com_smartforms/plugin/');
}

require_once (SMART_FORMS_PUBLIC_DIR.'classes/SmartFormsActionManager.php');
require_once (SMART_FORMS_PUBLIC_DIR.'classes/SmartFormsFilterManager.php');
require_once (SMART_FORMS_PUBLIC_DIR.'classes/SmartFormsOptionManager.php');


$db = JFactory::getDBO();
$db->setQuery("SELECT * FROM #__smart_forms_plugins");
$plugins = $db->loadObjectList();
foreach($plugins as $plugin)
{
    $rootFolder=$plugin->plugin_root_folder;
    $loader=$plugin->plugin_loader;

    $loaderPath=JPATH_SITE.'/'.$rootFolder.'/'.$loader;

    if(file_exists($loaderPath))
        require_once $loaderPath;
}
