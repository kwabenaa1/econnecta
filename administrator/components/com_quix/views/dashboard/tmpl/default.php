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
JHtml::_('bootstrap.tooltip');
JFactory::getDocument()->addScript(QUIX_URL . '/assets/js/Chart.js');

?>
<form action="<?php echo JRoute::_('index.php?option=com_quix'); ?>" method="post" name="adminForm" id="message-form" class="form-validate form-horizontal">
<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
   <div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif; ?>
    
  <!-- show warnings   -->
  <?php echo $this->loadTemplate('message') ?>

  <div class="row-fluid">
    <div class="span6">
      <div class="card">
        <div class="card-body"><?php echo $this->loadTemplate('overview') ?></div>
      </div>
    </div>
    <div class="span6">
      <?php echo $this->loadTemplate('pages') ?>
    </div>

  </div>

  <?php echo loadClassicBuilderFooterCredit(QuixHelper::isFreeQuix()); ?>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
