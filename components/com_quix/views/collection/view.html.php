<?php

/**
 * @version    CVS: 1.0.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * View to edit
 *
 * @since  1.6
 */
class QuixViewCollection extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app  = JFactory::getApplication();
		$user = JFactory::getUser();
		$dispatcher = JEventDispatcher::getInstance();
		
		$this->state  = $this->get('State');
		$this->item   = $this->get('Data');
		$this->params = $app->getParams('com_quix');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		if(isset($this->item->id) && $this->item->id)
		{
			// render quix content
			$page = quixRenderItem($this->item);
			
			$this->item->text = $page;
		}
		elseif(isset($_POST) && isset($_POST['layout']) )
		{	
			// render quix content
			$page = quixRenderItem($_POST['layout']);

			$this->item->id = 0;
			$this->item->text = $page;
		}
		else
		{
			JError::raiseError(404, JText::_('JERROR_PAGE_NOT_FOUND'));
			return false;
		}

		// Increment the hit counter of the product.
		// $model = $this->getModel();
		// $model->hit();
		
		parent::display($tpl);
	}
}
