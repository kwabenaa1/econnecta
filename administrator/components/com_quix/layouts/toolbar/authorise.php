<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; 
JHTML::_('behavior.modal');
$text = JText::_('COM_QUIX_TOOLBAR_ACTIVATION');
?>
<div class="qx-admin-box">
	<div class="display-table">
		<div class="table-cell table-content">
			<img src="<?php echo JUri::root(); ?>/libraries/quix/assets/images/quix-logo.png" width="60px">
		</div>
		<div class="table-cell table-content">
			<h4 class="text-uppercase">
				<?php echo JText::_('COM_QUIX_WELCOME'); ?>
			</h4>

			<p>
				<?php echo JText::_('COM_QUIX_AUTHORISE_MESSAGE'); ?>
			</p>
		</div>
		<div class="table-cell">
			<span class="pull-right">	
				<a
					rel="{handler:'iframe', size:{x:700,y:350}}"
					href="index.php?option=com_quix&amp;view=config&amp;tmpl=component"
					title="<?php echo $text; ?>" class="quixSettings btn btn-danger btn-small pink"
					id="mySettings2">
						<span class="icon-lock"></span> <?php echo $text; ?>
				</a>
			</span>
		</div>
	</div>
</div>
<?php if(JFactory::getApplication()->input->get('action', false)): ?>
<script type="text/javascript">
	setTimeout(function(){ 
		jQuery('.quixSettings')[0].click();
	}, 3000);
</script>
<?php endif; ?>