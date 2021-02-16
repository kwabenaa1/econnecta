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
?>
<?php
  // Joomla Component Creator code to allow adding non select list filters
  if (!empty($this->extra_sidebar))
  {
    $this->sidebar .= $this->extra_sidebar;
  }
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
    
    <?php echo QuixHelper::randerSysMessage(); ?>
    <?php echo QuixHelper::getFreeWarning(); ?>
    <?php echo QuixHelper::getUpdateStatus(); ?>
    <?php echo QuixHelper::proActivationMessage(); ?>
    <?php echo QuixHelper::askreview(); ?>
    <?php echo QuixHelper::webpCheck(); ?>

    <div class="row-fluid">
      <div class="span5">
        <?php echo $this->loadTemplate('req') ?>
        
        <div class="card support">
          <div class="card-header">
            <h3 class="card-title">Quick Fix</h3>
          </div> <!--card-header end-->
          <div class="card-body">
            <h4>Quix not working properly?</h4>
            <p>There are some extensions that might block Quix from working properly. Make sure you configure these types of extensions correctly.</p>

            <div class="alert">
              <ol>
                <li>Cache</li>
                <li>Security</li>
                <li>Optimization</li>
                <li>Htaccess</li>
              </ol>
            </div>

            <p class="well well-small"><strong>Solutions:</strong> Exclude <strong>libraries/quix/assets</strong> from settings or Exclude <strong>com_quix</strong> from your extensions exception list</p>

            <p class="well well-small"><strong>Clean page cache:</strong> To force clean all page assets cache, <a href="index.php?option=com_quix&task=cleanPageAssets">Click here</a></p>

          </div>
        </div>
      </div>
      <div class="span7">
        <div class="card support">
          <div class="card-header">
            <h3 class="card-title">Product Support</h3>
          </div> <!--card-header end-->
          <div class="card-body">
            <div class="media">
              <i class="icon-checkmark-circle"></i>
              <div class="media-body">
                <a href="https://www.themexpert.com/docs/quix-builder/basics/requirements" target="_blank" class="btn btn-primary btn-small pull-right">Read Now</a>
                <h3>Requirements</h3>
                <p>Quix has some system requirements to fill.</p>
              </div>
            </div>
            
            <div class="media">
              <i class="icon-arrow-up-4"></i>
              <div class="media-body">
                <a href="https://www.themexpert.com/docs/quix/getting-started/updating" target="_blank" class="btn btn-primary btn-small pull-right">Read Now</a>
                <h3>Update Classic Builder</h3>
                <p>Follow the step to update from Version 1.9.x</p>
              </div>
            </div>

            <div class="media">
              <i class="icon-arrow-up-4"></i>
              <div class="media-body">
                <a href="https://www.themexpert.com/docs/quix-builder/basics/installation" target="_blank" class="btn btn-primary btn-small pull-right">Read Now</a>
                <h3>Update Visual Builder</h3>
                <p>Follow the step to update using standard Quix Installer</p>
              </div>
            </div>

            <div class="media">
              <i class="icon-smiley-sad-2"></i>
              <div class="media-body">
                <a href="https://www.themexpert.com/docs/quix/tutorials/how-to-fix-quix-pages-loading-problem-on-your-website" target="_blank" class="btn btn-primary btn-small pull-right">Read Now</a>
                <h3>Builder Broken</h3>
                <p>If your builder doesn't load, follow these steps.</p>
              </div>
            </div>
            <div class="media">
              <i class="icon-folder-close"></i>
              <div class="media-body">
                <a href="https://www.themexpert.com/docs/quix/tutorials/fix-filemanager-403-access-denied-issue" target="_blank" class="btn btn-primary btn-small pull-right">Read Now</a>
                <h3>Filemanager Blank</h3>
                <p>Either its block or 403 access denied issue</p>
              </div>
            </div>
            <div class="media">
              <i class="icon-stack"></i>
              <div class="media-body">
                <a href="https://www.themexpert.com/docs/quix" target="_blank" class="btn btn-primary btn-small pull-right">Read Now</a>
                <h3>Online Documentation</h3>
                <p>The best start for Quix beginners and developers</p>
              </div>
            </div>
            <div class="media">
              <i class="icon-support"></i>
              <div class="media-body">
                <a href="https://www.themexpert.com/forum" target="_blank" class="btn btn-primary btn-small pull-right">Ask Now</a>
                <h3>Support Forum</h3>
                <p>Direct help from our qualified support team</p>
              </div>
            </div>
            <div class="media">
              <i class="icon-users"></i>
              <div class="media-body">
                <a href="https://www.facebook.com/groups/QuixUserGroup/" target="_blank" class="btn btn-primary btn-small pull-right">Join Now</a>
                <h3>Awesome Community</h3>
                <p>Join Quix facebook group and get help from others</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> <!--row end-->

	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>

</form>
