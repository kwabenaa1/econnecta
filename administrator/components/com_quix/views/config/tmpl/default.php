<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');

JFactory::getDocument()->addScriptDeclaration("
		Joomla.submitbutton = function(task)
		{
			if (task == 'config.cancel' || document.formvalidator.isValid(document.getElementById('config-form')))
			{
				Joomla.submitform(task, document.getElementById('config-form'));
			}
		};
");
?>
<form action="<?php echo JRoute::_('index.php?option=com_quix'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal qx-admin-box" style="box-shadow: none;">
	<div>
		<div class="modal-header" style="padding: 0;border: none;">
			<h3><?php echo JText::_('COM_QUIX_MY_SETTINGS');?></h3>
		</div>
		<div class="modal-body">
			
			<?php if(isset($this->item->activated) && $this->item->activated == 1): ?>
			<div class="qx-admin-box" style="background:#dff0d8;color:#3c763d;">
				<h3 style="margin: 0px;font-size: 16px;">Congratulations!</h3>
				<p style="margin: 0px;">Your license has been Activated. You can use Quix Pro features now. Enjoy</p>
			</div>
			<?php else: ?>
				<div class="alert alert-info">
					<?php echo JText::_('COM_QUIX_MY_SETTINGS_DESC'); ?>
				</div>
			<?php endif; ?>

			<fieldset>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('username'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('username'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('key'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('key'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="controls hide" data-message>Hey!</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button class="btn btn-primary btn-large" type="submit" onclick="Joomla.submitform('config.save', this.form);window.top.setTimeout('window.parent.jModalClose();location.reload();', 700);">
							<?php echo JText::_('JAPPLY');?>
						</button>
						<a href="#" class="btn btn-success btn-large" data-validation-submit>
							Activate <i class="icon-arrow-right-4"></i>
						</a>
					</div>
				</div>

			</fieldset>
			
		</div>
	</div>

	<?php echo $this->form->getInput('activated'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
<style type="text/css">
	.text-uppercase{
		text-transform: uppercase;
	}
	.qx-admin-box{
		padding: 20px;
		background: #fff;
		box-shadow: none;
		font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
		margin-bottom: 20px;
	    font-size: 14px;
	}
	.display-table{
		display: table;
		width: 100%;
	}
	.table-cell{
		display: table-cell;
	    vertical-align: middle;
	}
	.table-content{
		padding-right:20px; 
	}
	.qx-admin-box label{
		font-weight: bold;
	}
	.qx-admin-box input{
		background: #ebecec;
	    box-sizing: border-box;
	    color: #222;
	    display: block;
	    height: 36px;
	    padding: 5px 10px;
	    box-shadow: none;
	    border: 1px solid #f1f1f1;
	    font-size: 14px;
	    width: 80%;
	}
	.qx-admin-box .control-group .control-label{
	    line-height: 31px;
	    text-align: right;
	}
	.qx-admin-box fieldset{
		margin: 40px 0;
	}
	.qx-admin-box label{
		font-size: 14px;
	}
</style>