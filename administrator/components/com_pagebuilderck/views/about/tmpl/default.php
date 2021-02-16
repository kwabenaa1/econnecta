<?php
/**
 * @name		Page Builder CK
 * @package		com_pagebuilderck
 * @copyright	Copyright (C) 2015. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author		Cedric Keiflin - https://www.template-creator.com - https://www.joomlack.fr
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Pagebuilderck\CKFof;

CKfof::addStyleSheet(PAGEBUILDERCK_MEDIA_URI . '/assets/pagebuilderck.css');
?>
<div class="ckadminsidebar"><?php echo JHtmlSidebar::render() ?></div>
<div class="ckadminarea">
<?php
// check for the update
$latest_version = PagebuilderckHelper::getLatestVersion();
$isOutdated = PagebuilderckHelper::isOutdated();
if ($latest_version !== false) {
	if ($isOutdated) {
		echo '<p class="alertck">' . JText::_('CK_IS_OUTDATED') . ' : <b>' . $latest_version . '</b></p>';
	} else {
		echo '<p class="infock">' . JText::_('CK_IS_UPTODATE') . '</p>';
	}
}
?>
<style>
	.ckaboutversion {
		margin: 10px;
		padding: 10px;
		font-size: 20px;
		font-color: #000;
		text-align: center;
	}
	.ckcenter {
		margin: 10px 0;
		text-align: center;
	}
</style>
<div class="ckaboutversion"><?php echo JText::_('CK_PAGEBUILDERCK_VERSION') . ' ' . $this->ckversion; ?> <?php echo (PagebuilderckHelper::getParams() ? 'PRO' : 'LIGHT') ?></div>
<div class="ckcenter"><img src="<?php echo PAGEBUILDERCK_MEDIA_URI ?>/images/logo_pagebuilderck_large_48.png" /></div>
<p class="ckcenter"><a href="https://www.joomlack.fr" target="_blank">https://www.joomlack.fr</a></p>
<p class="ckcenter"><?php echo JText::_('CK_PAGEBUILDERCK_DESC'); ?></p>
<p class="ckcenter">Free FatCow-Farm Fresh Icons - https://www.fatcow.com/free-icons</p>
<div class="alert"><?php echo JText::_('COM_PAGEBUILDERCK_VOTE_JED'); ?>&nbsp;<a href="https://extensions.joomla.org/extensions/extension/authoring-a-content/content-construction/page-builder-ck" target="_blank" class="btn btn-small btn-warning"><?php echo JText::_('COM_PAGEBUILDERCK_VOTE_JED_BUTTON'); ?></a></div>
<div><?php echo PagebuilderckHelper::showParamsMessage(true, JText::_('CK_PAGEBUILDERCK_GETMORE_PRO_ITEMS')); ?></div>
<hr />
<?php
PagebuilderckHelper::displayReleaseNotes();
?>
</div>