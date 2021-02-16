<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');


if(isset($_REQUEST['view'])&&$_REQUEST['view']=='smartforms')
{
    require_once JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsLoader.php';
    $loader=new SmartFormsLoader();
    echo $formText=$loader->LoadForm($_REQUEST['id']);
}


if(JFactory::getApplication()->input->get('task','','raw')=='')
    return;
$task=JFactory::getApplication()->input->get('task','','raw');
if($task=='preview'){
    require_once JPATH_SITE.'/administrator/components/com_smartforms/plugin/SmartFormsLoader.php';
    $loader=new SmartFormsLoader();
    echo $formText=$loader->LoadForm(-1);
}