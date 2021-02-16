<?php
/**
 * @copyright	Copyright (C) 2015 Cédric KEIFLIN alias ced1870
 * https://www.template-creator.com
 * https://www.joomlack.fr
 * @license		GNU/GPL
 * */
 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');

class plgPagebuilderckAudio extends JPlugin {

	private $context = 'PLG_PAGEBUILDERCK_AUDIO';

	private $type = 'audio';

	function __construct(&$subject, $params) {

		parent::__construct($subject, $params);
	}

	/* 
	 * Construct the Menu Item to drag into the interface
	 *
	 * Return Object with item data
	 */
	public function onPagebuilderckAddItemToMenu() {
		// load the language files of the plugin
		$this->loadLanguage();
		// create the menu item
		$menuitem = new stdClass();
		$menuitem->type = $this->type;
		$menuitem->group = 'multimedia';
		$menuitem->title = JText::_($this->context . '_MENUITEM_TITLE');
		$menuitem->description = JText::_($this->context . '_MENUITEM_DESC');
		$menuitem->image = JUri::root(true) . '/plugins/pagebuilderck/audio/assets/images/audio.png';

		return $menuitem;
	}

	/* 
	 * Display the html code for the item to be used into the interface
	 *
	 * Return String the html code
	 */
	public function onPagebuilderckLoadItemContentAudio() {
		$input = JFactory::getApplication()->input;
		$id = $input->get('ckid', '', 'string');
		// ckstyle and inner classes are needed to get the styles from the interface
		?>
		<div id="<?php echo $id; ?>" class="cktype" data-type="audio">
			<div class="ckstyle">
			</div>
			<div class="inner">
				<div class="audiock">
					<img src="<?php echo PAGEBUILDERCK_MEDIA_URI ?>/images/audio-fake.jpg" width="300" height="auto" data-src="" />
			</div>
		</div>
		<?php
	}

	/* 
	 * Load the interface for the item edition
	 *
	 * Return String the html code
	 */
	public function onPagebuilderckLoadItemOptionsAudio() {
		// load the language files of the plugin
		$this->loadLanguage();
		// load the interface for the options
		$tpl = JPATH_SITE . '/plugins/pagebuilderck/audio/layouts/edit_audio.php';
		return $tpl;
	}

	/* 
	 * Display the html code for the item to be used into the frontend page
	 * @param string the item object from simple_html_dom
	 * 
	 * Return String the html code
	 */
	public function onPagebuilderckRenderItemAudio($item) {
		$attrs = $item->find('.tab_audio');
		$params = PagebuilderckFrontHelper::createParamsFromElement($attrs);

		$audiosrc = PagebuilderckFrontHelper::getSource($params->get('audiourl'));
		$html ='<audio style="width:100%;box-sizing:border-box;max-width:100%;" controls src="' . $audiosrc . '" ' . ($params->get('autoplayyes') == 'checked' ? 'autoplay' : '') . '>'
				. 'Your browser does not support the audio element.'
				. '</audio>';

		$html = preg_replace('#<div class="audiock"[^>]*>(.*?)<\/div>#is', $html, $item->innertext);

		return $html;
	}
}