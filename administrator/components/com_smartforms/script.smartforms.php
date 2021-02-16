<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU General Public License v3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class com_smartformsInstallerScript
{	
	function postflight($type, $parent)
	{
$db  = JFactory::getDbo();
		$query="UPDATE #__extensions SET `enabled`=1 WHERE `element`='loadsmartforms' AND `type`='plugin'";
		$db->setQuery($query);
		$db->execute();
		
		
		$db  = JFactory::getDbo();
		$query="UPDATE #__extensions SET `enabled`=1 WHERE `element`='publicajax' AND `type`='plugin'";
		$db->setQuery($query);
		$db->execute();
	}
	
	public function install($parent) 
    {
        echo "test";
    }
}