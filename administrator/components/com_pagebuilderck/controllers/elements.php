<?php
/**
 * @name		Page Builder CK
 * @package		com_pagebuilderck
 * @copyright	Copyright (C) 2015. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */

// No direct access.
defined('_JEXEC') or die;

use Pagebuilderck\CKController;
use Pagebuilderck\CKFof;

/**
 * Pages list controller class.
 */
class PagebuilderckControllerElements extends CKController {

	function __construct() {
		parent::__construct();
	}

	public function import() {
		exit('import not allowed');
		$app = JFactory::getApplication();
		if ($importClass = PagebuilderckHelper::getParams('import')) {
			$importClass->importFile();
		} else {
			$msg = JText::_('CK_PAGEBUILDERCK_PARAMS_NOT_FOUND');
			$app->redirect("index.php?option=com_pagebuilderck&view=elements", $msg, 'error');
			return false;
		}
	}

	public function export() {
		exit('export not allowed');
		$app = JFactory::getApplication();
		if ($exportClass = PagebuilderckHelper::getParams('export')) {
			$exportClass->exportFile();
		} else {
			$msg = JText::_('CK_PAGEBUILDERCK_PARAMS_NOT_FOUND');
			$app->redirect("index.php?option=com_pagebuilderck&view=elements", $msg, 'error');
			return false;
		}
	}

	public function order() {
		// security check
		CKFof::checkAjaxToken();

		$ordering = $this->input->get('ordering', '', 'array');
		$model = $this->getModel('elements');
		$result = $model->saveOrder($ordering);
		echo (int) $result;
		exit;
	}
}