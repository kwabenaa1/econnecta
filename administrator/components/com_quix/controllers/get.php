<?php
/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * pages Component Message Model
 *
 * @since  1.6
 */
class QuixControllerGet extends JControllerLegacy
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function hasImage()
	{
		// Reference global application object
		$app = JFactory::getApplication();

		// JInput object
		$input = $app->input;

		// Requested format passed via URL
		$format = strtolower($input->getWord('format', 'json'));

		// Requested element name
		$path = strtolower($input->get('path', '', 'string'));

		// check if path passed
		if( !$path )
	   	{
	   		$results = new InvalidArgumentException(JText::_('COM_QUIX_NO_ARGUMENT'), 403);
	   	}

		// first check if its from default template
		if ( is_file( JPATH_ROOT . $path ) )
		{
			$results = true;
		}
		else
		{
			$results = new InvalidArgumentException(JText::_('COM_QUIX_FILE_NOT_EXISTS'), 404);
		}

		// return result
		echo new JResponseJson($results , null, false, $input->get('ignoreMessages', true, 'bool'));

		$app->close();
	}

	/**
	 * Method to handle file manager operation
	 *
	 * @return  object
	 *
	 * @since   2.0
	 */
	function uploadMedia()
	{
		// Check for request forgeries.
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

		(new \FileManager\FileManager(JPATH_COMPONENT_SITE . '/filemanager/config.php'));
        exit;
	}

	/**
	 * Method to handle file manager operation
	 *
	 * @return  object
	 *
	 * @since   2.0
	 */
	function reviewLater()
	{
		$app = JFactory::getApplication();

		$time = time() + 604800; // 1 week
		
		$app->input->cookie->set('reviewLater', true, $time, $app->get('cookie_path', '/'), $app->get('cookie_domain'), $app->isSSLConnection());
		
		echo new JResponseJson('Next time. Thank you');
		jexit();
	}

	/**
	 * Method to handle file manager operation
	 *
	 * @return  object
	 *
	 * @since   2.0
	 */
	function reviewDone()
	{
		$app = JFactory::getApplication();
		// Set the cookie
		// $time = time() + 60 48 00; // 1 week
		$time = time() + 7776000; // 3 months
		$app->input->cookie->set('reviewDone', true, $time, $app->get('cookie_path', '/'), $app->get('cookie_domain'), $app->isSSLConnection());

		echo new JResponseJson('Thank you');
		jexit();
	}

	function disableEditor()
	{
		require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';

        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->get('quixEditorMapID', '', 'int');

        $result = QuixHelperEditor::disableEditor($id);
		
		if($result){
			echo new JResponseJson(true);
		}else{
			$e = new Exception(false);
			echo new JResponseJson($e);
		}
		jexit();

	}

	function enableEditor()
	{
		require_once JPATH_SITE . '/components/com_quix/helpers/editor.php';

        $app = JFactory::getApplication();
        $input = $app->input;
        $id = $input->get('quixEditorMapID', '', 'int');

        $result = QuixHelperEditor::enableEditor($id);
		
		if($result){
			echo new JResponseJson(true);
		}else{
			$e = new Exception(false);
			echo new JResponseJson($e);
		}
		jexit();

	}


}
