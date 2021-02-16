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
<div class="modal fade hide" id="quixModalPageSEO" tabindex="-1" role="options" aria-labelledby="quixModalPageOptions" style="display:none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">SEO</h4>
      </div>
      <div class="modal-body">
        <?php echo $this->form->getControlGroup('metadata'); ?>
        <?php foreach ($this->form->getGroup('metadata') as $field) : ?>
          <?php echo $field->getControlGroup(); ?>
        <?php endforeach; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Save changes</button>
      </div>
    </div>
  </div>
</div>  