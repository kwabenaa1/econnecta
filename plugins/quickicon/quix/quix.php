<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Quickicon.quix
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! update notification plugin
 *
 * @since  2.5
 */
class PlgQuickiconQuix extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  3.1
	 */
	protected $autoloadLanguage = true;

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param   string  $context  The calling context
	 *
	 * @return  array  A list of icon definition associative arrays, consisting of the
	 *                 keys link, image, text and access.
	 *
	 * @since   2.5
	 */
	public function onGetIcons($context)
	{
		if ($context !== $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_installer'))
		{
			return;
		}

		JHtml::_('jquery.framework');

		$cur_template = JFactory::getApplication()->getTemplate();

		$token    = JSession::getFormToken() . '=' . 1;
		$ajax_url = JUri::base() . 'index.php?option=com_quix&view=pages&task=updateAjax&' . $token;
		$script = array();
		$script[] = 'var plg_quickicon_quix_ajax_url = \'' . $ajax_url . '\';';
		$script[] = 'var plg_quickicon_quix_text = {'
			. '"UPTODATE" : "' . JText::_('PLG_QUICKICON_QUIX_UPTODATE', true) . '",'
			. '"ERROR": "' . JText::_('PLG_QUICKICON_QUIX_ERROR', true) . '",'
			. '};';
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		JHtml::_('script', JUri::root() . 'plugins/quickicon/quix/update.js', array('version' => 'auto', 'relative' => true));

		return array(
			array(
				'link' => 'index.php?option=com_quix',
				'image' => 'puzzle',
				'text' => JText::_('PLG_QUICKICON_QUIX_UPDATING'),
				'id' => 'plg_quickicon_quix',
				'group' => 'MOD_QUICKICON_MAINTENANCE'
			)
		);
	}
}
