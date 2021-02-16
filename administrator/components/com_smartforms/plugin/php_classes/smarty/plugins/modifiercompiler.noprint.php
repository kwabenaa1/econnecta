<?php
/**
 * Smart Forms jor Joomla
 * @license Released under the terms of the GNU LESSER GENERAL PUBLIC LICENSE Version 3
 **/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsModifierCompiler
 */

/**
 * Smarty noprint modifier plugin
 * Type:     modifier<br>
 * Name:     noprint<br>
 * Purpose:  return an empty string
 *
 * @author   Uwe Tews
 * @return string with compiled code
 */
function smarty_modifiercompiler_noprint()
{
    return "''";
}
