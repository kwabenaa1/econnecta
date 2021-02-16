<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

// Base this model on the backend version.
JLoader::register('QuixModelPage', JPATH_ADMINISTRATOR . '/components/com_quix/models/page.php');
require_once JPATH_COMPONENT . '/helpers/theme.php';

/**
 * Quix Component page Model
 *
 * @since  1.5
 */
class QuixModelForm extends QuixModelPage
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$this->typeAlias = $input->get('type', 'page');
		
		// Load state from the request.
		$pk = $app->input->getInt('id');
		$this->setState($this->typeAlias.'.id', $pk);
		$this->setState('com_quix.edit.form.id', $pk);

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $app->input->getString('layout'));
	}

	/**
	 * Method to get page data.
	 *
	 * @param   integer  $itemId  The id of the page.
	 *
	 * @return  mixed  Quix item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState($this->typeAlias . '.id');

		// Get a row instance.
		$table = $this->getTable();
		
		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());

			return false;
		}

		$properties = $table->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'JObject');

		// Convert attrib field to Registry.
		$value->params = new Registry($value->params);

		// Compute selected asset permissions.
		$user   = JFactory::getUser();
		$userId = $user->get('id');
		$asset  = 'com_quix.' . $this->typeAlias . $value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			$value->params->set('access-edit', true);
		}

		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset))
		{
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_by)
			{
				$value->params->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId)
		{
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else
		{
			$value->params->set('access-change', $user->authorise('core.edit.state', 'com_quix'));
		}

		// Convert the metadata field to an array.
		$registry = new Registry;
		$registry->loadString($value->metadata);
		$value->metadata = $registry->toArray();

		if(isset($value->type)){
			$conditions = QuixHelperTheme::getAll($value->id, $value->type);
			if(count($conditions)){
				$arrayOfConditions = [];
				foreach ($conditions as $key => $item) {
					$arrayOfConditions[] = json_decode($item->params);
				}
				$value->conditions = json_encode($arrayOfConditions);
			}
		}
		
		return $value;
	}

	/**
	 * Get the return URL.
	 *
	 * @return  string	The return URL.
	 *
	 * @since   1.6
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.2
	 */
	public function save($data)
	{
		// now validate json
		// since 2.6.0
		$validJson = json_decode($data['data']);
		if($validJson === null) {
			// $validJson is null because the json cannot be decoded
			$err = new Exception('Invalid Content!!! Your data has been truncated by the server. Please change them or contact our support. Thank you');
			echo new JResponseJson($err);
			JFactory::getApplication()->close();
		}


		$data['builder_version'] = QUIX_VERSION;
		// Associations are not edited in frontend ATM so we have to inherit them
		// if (JLanguageAssociations::isEnabled() && !empty($data['id'])
		// 	&& $associations = JLanguageAssociations::getAssociations('com_quix', '#__quix', 'com_quix.item', $data['id']))
		// {
		// 	foreach ($associations as $tag => $associated)
		// 	{
		// 		$associations[$tag] = (int) $associated->id;
		// 	}

		// 	$data['associations'] = $associations;
		// }

		$input = JFactory::getApplication()->input;

		// if (isset($data['metadata']))
		// {
		// 	$registry = new Registry;
		// 	$registry->loadArray($data['metadata']);
		// 	$data['metadata'] = (string) $registry;
		// }
		
		$type = $input->get('type', 'page');

		if($type == 'collection')
		{
			if(empty($data['id'])){
				$data['uid'] = md5(uniqid(rand(), true));
			}elseif (empty($data['uid'])) {
				$data['uid'] = md5(uniqid(rand(), true));
			}

		}

		if (isset($data['params']) and isset($data['params']['code']))
		{
			// Make sure EOL is Unix
			$data['params']['code'] = str_replace(array("\r\n", "\r"), "\n", $data['params']['code']);
		}

		$data['builder'] = 'frontend';

		// save conditions data
		if(isset($data['conditions']) and !empty($data['conditions'])){
			$conditions = json_decode($data['conditions']);
			$response = [];
			foreach ($conditions as $key => $condition) 
			{
				$info = [];
				$info['condition_type'] = $condition->type;
				switch ($condition->type) {
					case 'article':
						$info['condition_id'] = 0;
						$info['condition_info'] = '';
						
						break;

					case 'all-menu':
						$info['condition_id'] = 0;
						$info['condition_info'] = '';
						
						break;

					case 'categories':
						$info['condition_id'] = $condition->subType;
						$info['condition_info'] = '';
						
						break;
					
					case 'menus':
					default:
						$info['condition_id'] = $condition->parent_menu;
						$info['condition_info'] = $condition->subType;

						break;					
				}
				
				$info['params'] = $condition;
				
				$response[] = QuixHelperTheme::log($data['id'], $data['type'], (array) $info);
			}
			
			// remove old unused records
			QuixHelperTheme::removeConditionsByIds($data['id'], $response);
		}
		
		return parent::save($data);
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$type = $input->get('type', 'page');

		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState($type.'.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
	}
	
	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$type = $input->get('type', 'page');

		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState($type.'.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Allows preprocessing of the JForm object.
	 *
	 * @param   JForm   $form   The form object
	 * @param   array   $data   The data to be merged into the form object
	 * @param   string  $group  The plugin group to be executed
	 *
	 * @return  void
	 *
	 * @since   3.7.0
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'Quix')
	{
		$params = $this->getState()->get('params');

		if ($params && $params->get('enable_category') == 1)
		{
			$form->setFieldAttribute('catid', 'default', $params->get('catid', 1));
			$form->setFieldAttribute('catid', 'readonly', 'true');
		}

		return parent::preprocessForm($form, $data, $group);
	}

	/**
	 * Get an instance of JTable class
	 *
	 * @param   string  $type    Name of the JTable class to get an instance of.
	 * @param   string  $prefix  Prefix for the table class name. Optional.
	 * @param   array   $config  Array of configuration values for the JTable object. Optional.
	 *
	 * @return  JTable|bool JTable if success, false on failure.
	 */
	public function getTable($type = '', $prefix = 'QuixTable', $config = array())
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$type = $input->get('type', 'page');

		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_quix/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   3.2
	 */
	public function validate($form, $data, $group = null)
	{
		// Filter and validate the form data.
		$data   = $form->filter($data);
		$return = $form->validate($data, $group);

		// Check for an error.
		if ($return instanceof Exception)
		{
			JFactory::getApplication()->enqueueMessage($return->getMessage(), 'error');

			return false;
		}

		// Check the validation results.
		if ($return === false)
		{
			// Get the validation messages from the form.
			foreach ($form->getErrors() as $message)
			{
				if ($message instanceof Exception)
				{
					$message = $message->getMessage();
				}

				JFactory::getApplication()->enqueueMessage($message, 'error');
			}

			return false;
		}
		
		if(!isset($data['id'])){
			$data['id'] = 0;
		}

		return $data;
	}
}