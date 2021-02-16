<?php
/**
 * @version    1.8.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access

defined('_JEXEC') or die;
// Load jQUery
JHtml::_('jquery.framework');
// Load Bootstrap 3
JHtml::_('bootstrap.framework');
// Keep alive
JHtml::_('behavior.keepalive');
// Quix autoloader
jimport('quix.vendor.autoload');
$version = 'ver=' . QUIX_VERSION;

// // Builder css
JFactory::getDocument()->addStylesheet(QUIX_URL . '/assets/css/qxbs.css?' . $version);
JFactory::getDocument()->addStylesheet(QUIX_URL . '/assets/css/qx-fb.css?' . $version);
JFactory::getDocument()->addStylesheet(QUIX_URL . '/assets/css/qxui.css?' . $version);
JFactory::getDocument()->addStylesheet(QUIX_URL . '/assets/css/qxicon.css?' . $version);
JFactory::getDocument()->addStylesheet(QUIX_URL . '/assets/css/qxkit.css?' . $version);
JFactory::getDocument()->addStylesheet(QUIX_URL . '/assets/css/qxi.css?' . $version);
JFactory::getDocument()->addStylesheet(QUIX_URL . '/assets/css/quix.css?' . $version);
// fallback force need for summernotes editor
JFactory::getDocument()->addStylesheet(JUri::root() . 'media/jui/css/bootstrap.min.css?' . $version);

// Load js
JFactory::getDocument()->addScript(QUIX_URL . '/assets/js/cookies.js?' . $version);
JFactory::getDocument()->addScript(JUri::root(true) . '/media/quix/assets/iframe.js?' . $version, ['version' => 'auto']);
JFactory::getDocument()->addScript(QUIX_URL . '/assets/js/jquery-ui.js?' . $version);
JFactory::getDocument()->addScript(QUIX_URL . '/assets/js/jquery-scrollto.js?' . $version);
JFactory::getDocument()->addScript(QUIX_URL . '/assets/js/qxkit.js?' . $version);

// Summernote Editor
JFactory::getDocument()->addScript(QUIX_URL . '/assets/js/summernote.js?' . $version);
JFactory::getDocument()->addScriptDeclaration('(function($) {$(window).load(function(){
  quixHeartBeatApi.init("' . JUri::root() . 'index.php?option=com_quix&task=live&' . JSession::getFormToken() . '=1' . '")
});})(jQuery);');
?>

<?php echo $this->loadTemplate('modal'); ?>

<div class="qx-fb-frame">
  <div id="qx-fb-frame-toolbar"></div>
  <div class="qx-fb-frame-preview" data-preview="desktop">

    <div class="qx-fb-frame-preview-responsive-wrapper">
      <iframe src="<?php echo $this->iframeUrl; ?>" frameborder="0"
        style="width:100%;height: 100vh;margin-top: 53px; padding-bottom: 53px;" name="quixframe"
        id="quix-iframe-wrapper"
        <?php if ($this->config->get('fix_iframeloading', 0)): ?>
        sandbox="allow-top-navigation-by-user-activation allow-forms allow-popups allow-modals allow-pointer-lock allow-same-origin allow-scripts"
        <?php endif; ?>
        allowfullscreen="1">
      </iframe>
    </div>

  </div>
  <form
    action="<?php echo JRoute::_('index.php?option=com_quix&view=' . $this->type . '&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="qx-fb form-validate"
    style="display: none;">
    <input type="hidden" name="jform[id]" id="jform_id" value="<?php echo $this->item->id; ?>" />
    <input type="hidden" id="jform_Itemid" value="<?php echo $this->Itemid;?>" />

    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
    <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />
    <input type="hidden" id="jform_task" name="task" value="" />
    <input type="hidden" id="jform_type" name="type" value="<?php echo $this->type ?>" />
    <input type="hidden" id="jform_return" name="return"
      value="<?php echo base64_encode(JRoute::_('index.php?option=com_quix&view=' . $this->type . '&id=' . (int) $this->item->id . ($this->Itemid ? '&Itemid=' . $this->Itemid : ''))); ?>" />

    <?php if (isset($this->item->type)): ?>
    <input type="hidden" id="jform_template_type" name="jform[type]" value="<?php echo $this->item->type ?>" />
    <?php endif; ?>

    <input type="hidden" id="jform_builder_version" name="jform[builder_version]"
      value="<?php echo $this->item->builder_version ?>" />
    <input type="hidden" id="jform_token" name="<?php echo JSession::getFormToken(); ?>" value="1" />

  </form>
</div>
<div id="filemanager"></div>
<script type="text/javascript">
  window.Beacon('init', '54867f3a-1255-4bc6-8ca1-d2e5caa4b237');
</script>
<div id="hidden-for-editor" style="display: none!important;">
  <?php echo $this->form->renderField('editor'); ?>
</div>
<style>
  .com_quix.layout-edit .qxui-modal-wrap.qxui-modal--template div.qxui-modal {
    left: calc(50vw - 575px);
  }

  .com_quix.layout-edit .qxui-modal-wrap div.qxui-modal {
    left: calc(50vw - 260px);
    top: calc(50vh - 300px);
  }

  .com_quix.layout-edit .qxui-modal-wrap.fm-modal.qxui-modal--with-tab .qxui-modal {
    left: 50%;
    margin-left: -600px;
    top: 100px;
  }
  .com_quix.layout-edit .qxui-modal-wrap.fm-plugincontainer .qxui-modal{
    top: 200px;
  }
</style>
