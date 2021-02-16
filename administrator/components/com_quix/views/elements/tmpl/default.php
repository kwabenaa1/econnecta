<?php
/**
 * @version    1.1.0
 * @package    com_quix
 * @author     ThemeXpert <info@themexpert.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_quix'); ?>" name="adminForm" id="adminForm" class="form-validate form-horizontal">
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

      <div class="clearfix"></div>

      <h2>1.x Available Elements</h2>
      <hr>

      <ul class="qx-elements-list clearfix">
        <?php $elements = quix()->getElements(); ?>
        <?php foreach ($elements as $element): ?>
          <li class="qx-element-list__item">
            <div class="success-message hide"><?php echo JText::_('JSAVED') ?></div>
            <div class="switch">
              <label>
                <img class="qx-element__icon" src="<?php echo $element['thumb_file'] ?>" alt="Accordion">
                <h4 class="qx-element__title"><?php echo $element['name'] ?></h4>
                <input data-element-slug="<?php echo $element['slug'] ?>" type="checkbox"  <?php echo $element['enabled'] ? 'checked' : ''?>>
                <span class="lever"></span>
              </label>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      
      <?php // echo QuixHelper::getProElementBanner(); ?>

      <?php echo loadClassicBuilderFooterCredit(QuixHelper::isFreeQuix()); ?>
        
      <?php echo JHtml::_('form.token'); ?>
      
    </div>

    <script>
      jQuery(function ($) {
        var token = '<?php echo JSession::getFormToken(); ?>';
        $('[data-element-slug]').on('change', function () {
          var item = jQuery(this);
          $.ajax({
            url: 'index.php?option=com_quix&task=updateElement',
            type: 'post',
            data: { status: this.checked ? 1:0, alias: this.dataset.elementSlug, token: token },
            beforeSend: function(){
              item.parent().parent().parent().addClass('disabled');
              item.attr('disabled', true);
            },
            success: function (res) {
              if(!res.success){
                console.log(res.data);
              }
              item.parent().parent().parent().removeClass('disabled');
              item.attr('disabled', false);
              item.parent().parent().parent().find('.success-message').fadeIn('fast').delay(1000).fadeOut('fast');
            }
          });
        });
      });
    </script>
</form>
