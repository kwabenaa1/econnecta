<?php
// No direct access
defined('_JEXEC') or die;

use Pagebuilderck\CKController;
use Pagebuilderck\CKFof;

class PagebuilderckControllerBrowse extends CKController {

	function __construct() {
		parent::__construct();
	}

	public function ajaxCreateFolder() {
		// security check
		CKFof::checkAjaxToken();

		if (CKFof::userCan('create', 'com_media')) {
			$path = $this->input->get('path', '', 'string');
			$name = $this->input->get('name', '', 'string');

			require_once PAGEBUILDERCK_PATH . '/helpers/ckbrowse.php';
			if ($result = CKBrowse::createFolder($path, $name)) {
				$msg = JText::_('CK_FOLDER_CREATED_SUCCESS');
			} else {
				$msg = JText::_('CK_FOLDER_CREATED_ERROR');
			}

			echo '{"status" : "' . ($result == false ? '0' : '1') . '", "message" : "' . $msg . '"}';
		} else {
			echo '{"status" : "2", "message" : "' . JText::_('CK_ERROR_USER_NO_AUTH') . '"}';
		}
		exit;
	}

	/**
	 * Get the file and store it on the server
	 * 
	 * @return mixed, the method return
	 */
	public function ajaxAddPicture() {
		// security check
		CKFof::checkAjaxToken();

		require_once PAGEBUILDERCK_PATH . '/helpers/ckbrowse.php';
		CKBrowse::ajaxAddPicture();
	}
}