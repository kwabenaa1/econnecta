<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$activated = QuixHelper::isProActivated();
$text = JText::_('COM_QUIX_TOOLBAR_ACTIVATION');
if($activated) $text = JText::_('COM_QUIX_TOOLBAR_ACTIVATION_DONE');
?>
<a
	rel="{handler:'iframe', size:{x:800,y:450}}"
	href="index.php?option=com_quix&amp;view=config&amp;tmpl=component"
	title="<?php echo $text; ?>" class="quixSettings btn btn-small hasTooltip <?php echo ($activated ? 'activated' : '')?>"
	id="mySettings"
	style="min-width: 0px;margin: 0;text-align: center;">
		<?php if($activated): ?>
		<i class="icon-ok" style="margin: 0px;"></i> 
		<?php else: ?>
		<i class="icon-lock" style="margin: 0px;"></i> 
		<?php endif; ?>
</a>
