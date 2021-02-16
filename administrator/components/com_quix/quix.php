<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined( '_JEXEC' ) or die;

// Access check.
if ( !JFactory::getUser()->authorise( 'core.manage', 'com_quix' ) ) {
  throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Include dependancies
jimport( 'quix.app.bootstrap' );

define('QUIX_BUILDER_TYPE', "classic");

global $QuixBuilderType;
$QuixBuilderType = "classic";

jimport( 'quix.app.init' );

$controller = JControllerLegacy::getInstance( 'Quix' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();
