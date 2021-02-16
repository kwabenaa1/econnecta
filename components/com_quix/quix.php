<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;

// Version warning
if (version_compare(phpversion(), '5.6', '<')) 
{
	$lang = JFactory::getLanguage();
	$extension = 'com_quix';
	$base_dir = JPATH_ADMINISTRATOR;
	$language_tag = 'en-GB';
	$reload = true;
	$lang->load($extension, $base_dir, $language_tag, $reload);

	$layout = new JLayoutFile('toolbar.phpwarning', JPATH_COMPONENT_ADMINISTRATOR . '/layouts');
	echo $layout->render(array());
	return true;
}

// Get App instance
$app = JFactory::getApplication();
$input = $app->input;

if ($input->get('view', '') === 'form')
{
	$user  = JFactory::getUser();
	// Can create in any category (component permission) or at least in one category
	$canCreateRecords = $user->authorise('core.create', 'com_quix')
	 || count($user->getAuthorisedCategories('com_quix', 'core.create')) > 0;

	// Instead of checking edit on all records, we can use **same** check as the form editing view
	$values = (array) $app->getUserState('com_quix.edit.form.id');
	$isEditingRecords = count($values);
	
	$hasAccess = $canCreateRecords || $isEditingRecords;
	
	if (!$hasAccess)
	{
		$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
		
		if ($user->get('guest'))
		{
			$return = base64_encode(JUri::getInstance());
			$login_url_with_return = 'index.php?option=com_users&return=' . $return;
			$app->redirect($login_url_with_return, 403);
		}
		else
		{
			$app->setHeader('status', 403, true);
			$app->redirect(JRoute::_('index.php'), 403);
		}
	}
}

// Include dependencies
jimport( 'quix.app.bootstrap' );

global  $QuixBuilderType ;
$QuixBuilderType = "frontend";
jimport( 'quix.app.init' );

JLoader::register( 'QuixFrontendHelper', JPATH_COMPONENT . '/helpers/quix.php' );
JLoader::register( 'QuixHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/quix.php' );

// Execute the task.
$controller = JControllerLegacy::getInstance( 'Quix' );
$controller->execute( $app->input->get( 'task' ) );
$controller->redirect();