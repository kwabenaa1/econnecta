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
?>
<!-- Modal -->
<div class="modal fade hide" id="quixModalPageOptions" tabindex="-1" role="options" aria-labelledby="quixModalPageOptions" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Page Options</h4>
      </div>
      <div class="modal-body">
        <div id="pageoption-tab-wrapper">
          <!-- Nav tabs -->
          <ul class="nav nav-tabs" role="tablist" id="pageoption-tab-list">
            <li role="presentation" class="active">
              <a href="#params" aria-controls="params" role="tab" data-toggle="tab">Params</a>
            </li>
            <!-- <li role="presentation">
              <a href="#pageacl" aria-controls="pageacl" role="tab" data-toggle="tab">ACL</a>
            </li> -->
          </ul>

          <!-- Tab panes -->
          <div class="tab-content" id="pageoption-tab-content">
            <div role="tabpanel" class="tab-pane active" id="params">
              <?php echo $this->form->getControlGroup('params'); ?>
              <?php foreach ($this->form->getGroup('params') as $field) : ?>
                <?php echo $field->getControlGroup(); ?>
              <?php endforeach; ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="pageacl">
              <?php if (JFactory::getUser()->authorise('core.admin','quix')) : ?>
                <?php echo $this->form->getInput('rules'); ?>
              <?php endif; ?>
            </div>
        
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Save changes</button>
      </div>
    </div>
  </div>
</div>  