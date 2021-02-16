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
<div id="quixBuilderLoadingFailed" qx-modal>
  <div class="qx-modal-dialog">
    <button class="qx-modal-close-default" type="button" qx-close></button>
    <div class="qx-modal-header">
      <h2 class="qx-modal-title" id="quixBuilderLoadingFailedTitle">Something went wrong!</h2>
    </div>
    <div class="qx-modal-body qx-alert qx-alert-danger">
      <div class="qx-card qx-alert-danger qx-p-3">
        <p class="qx-m-0">
          Please check
          <a href="https://www.themexpert.com/docs/quix/getting-started/requirements" target="_blank">
            <strong>Quix System Requirements</strong>
          </a>
          and follow the
          <a href="https://www.themexpert.com/docs/quix/tutorials/how-to-fix-quix-pages-loading-problem-on-your-website"
            target="_blank">
            <strong>Troubleshooting Guide</strong>
          </a>
          to fix the issue. If necessary contact our support to check the issue for you.
        </p>
        <?php $max_time = ini_get('max_execution_time'); ?>
        <?php if ($max_time < 300): ?>
        <p class="qx-alert qx-alert-warning">
          Your PHP settings <strong>max_execution_time</strong> is low <strong><?php echo $max_time ?></strong>. Please
          increase it to
          mininum <strong>300</strong>.
        </p>
        <?php endif; ?>
      </div>
    </div>
    <div class="qx-modal-footer qx-text-right">
      <button onclick="location.reload();" class="qx-button qx-button-success qx-modal-close">Reload</button>
      <button onclick="window.history.back();" class="qx-button qx-button-default">Back</button>
    </div>
  </div>
</div>

<!-- Preloader -->
<div class="preloader">
  <div class="wrap">
    <div class="ball"></div>
    <div class="ball"></div>
    <div class="ball"></div>
    <div class="ball"></div>
  </div>
  <p id="loaderMessage">Initializing Builder</p>
  <p class="qx-hints hide qx-hide text-hide"><?php echo QuixFrontendHelper::getHints(); ?></p>
</div>
<!-- Preloader -->
