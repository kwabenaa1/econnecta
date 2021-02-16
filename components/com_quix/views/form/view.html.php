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
require_once JPATH_COMPONENT_SITE . '/helpers/route.php';

/**
 * HTML page View class for the quix component
 *
 * @since  1.5
 */
class QuixViewForm extends JViewLegacy
{
	protected $form;

	protected $item;

	protected $return_page;

	protected $state;

	protected $type;

	protected $config;

	/**
	 * Should we show a captcha form for the submission of the page?
	 *
	 * @var   bool
	 * @since 3.7.0
	 */
	protected $captchaEnabled = false;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$user = JFactory::getUser();
		$app  = JFactory::getApplication();
		
		// TODO make it optional, as we dont have RTL support yet for builder
		JFactory::getDocument()->setDirection('ltr');

		// get the builder type
		$this->type = $app->input->get('type', 'page');
		
		$this->config = JComponentHelper::getComponent('com_quix')->params;
		
		// Get model data.
		$this->state       = $this->get('State');
		$this->item        = $this->get('Item');

		$this->form        = $this->get('Form');
		$this->return_page = $this->get('returnpage');

		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_quix') || count($user->getAuthorisedCategories('com_quix', 'core.create'));
		}
		else
		{
			$authorised = $this->item->params->get('access-edit');
		}

		if ($authorised !== true)
		{
			if ($user->get('guest'))
			{
				$return = base64_encode(JUri::getInstance());
				$login_url_with_return = 'index.php?option=com_users&return=' . $return;
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'notice');
				$app->redirect($login_url_with_return, 403);
			}
			else
			{
				$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
				$app->setHeader('status', 403, true);
				return;
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));

			return false;
		}

		// Create a shortcut to the parameters.
		$params = &$this->state->params;

		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx'));

		$this->params = $params;

		// Override global params with page specific params
		$this->params->merge($this->item->params);
		$this->user   = $user;

		// Propose current language as default when creating new page
		if (empty($this->item->id) && JLanguageMultilang::isEnabled())
		{
			$lang = JFactory::getLanguage()->getTag();
			$this->form->setFieldAttribute('language', 'default', $lang);
		}

		$captchaSet = $params->get('captcha', JFactory::getApplication()->get('captcha', '0'));

		foreach (JPluginHelper::getPlugin('captcha') as $plugin)
		{
			if ($captchaSet === $plugin->name)
			{
				$this->captchaEnabled = true;
				break;
			}
		}

		$route = QuixFrontendHelperRoute::getPageRoute($this->item->id);
		$uri = JURI::getInstance( $route );
		$this->Itemid = $uri->getVar('Itemid', '');
		
		if(!$this->Itemid){
			$menu = JMenu::getInstance('site');
			// there are no menu Itemid found, lets dive into menu finder
			$menuItem = $menu->getItems('link', 'index.php?option=com_quix&view=page&id='.$this->item->id, true);
			if(isset($menuItem->id)){
				$this->Itemid = $menuItem->id;
			}
		}
		$tmpl = $app->input->get('output', 'component') == 'component' ? '&tmpl=component' : '';
        $id = (int) $this->item->id;
		$this->iframeUrl = JUri::root() . 'index.php?option=com_quix&view=form&layout=iframe&builder=frontend&type=' . $this->type . '&id=' . $id . $tmpl;

		jimport( 'quix.app.init' );

		$this->_prepareDocument();
		$this->addPageList();
		parent::display($tpl);
	}
	
	/**
	 * Prepares the pagelist
	 * added to header as script
	 * @return  void
	 */
	protected function addPageList()
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		if($this->type == 'page')
		{
			$table = '#__quix';
			$fields = array('id', 'title', 'state');
		}
		else
		{
			$table = '#__quix_collections';
			$fields = array('id', 'title', 'type', 'state');
		}

		$query->select($fields)
			  ->from($table)
			  ->order('id desc')
			  ->where('state in (0, 1, 2)')
			  ->where('builder = "frontend"')
			  ->setLimit(999);

		if($this->item->id){
			$query->where('id != ' . $this->item->id);
		}

		$db->setQuery($query);
		$list = $db->loadObjectList();
		$data = json_encode($list);

		if($this->type == 'page')
		{
			$menu = JMenu::getInstance('site');
			// there are no menu Itemid found, lets dive into menu finder
			$menuItem = $menu->getItems('link', 'index.php?option=com_quix&view=page&id='.$this->item->id, true);

			if(isset($menuItem->id)){
				$hasMenu = true;
			}else{
				$hasMenu = false;
			}
	
			JFactory::getDocument()->addScriptDeclaration('var QuixPageHasMenu = "' . $hasMenu . '";');
			JFactory::getDocument()->addScriptDeclaration('var QuixPageList = ' . $data . ';');
			JFactory::getDocument()->addScriptDeclaration('var QuixCollectionList = [];');
		}
		else
		{
			JFactory::getDocument()->addScriptDeclaration('var QuixPageHasMenu = "false";');
			JFactory::getDocument()->addScriptDeclaration('var QuixPageList = [];');
			JFactory::getDocument()->addScriptDeclaration('var QuixCollectionList = ' . $data . ';');
		}
		
		JFactory::getDocument()->addScriptDeclaration('var QuixBuilderType = "' . $this->type . '";');

	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 */
	protected function _prepareDocument()
	{
		$app   = JFactory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('com_quix_form_edit_page'));
		}

		$title = $this->params->def('page_title', JText::_('com_quix_form_edit_page'));

		if ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		$pathway = $app->getPathWay();
		$pathway->addItem($title, '');

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
