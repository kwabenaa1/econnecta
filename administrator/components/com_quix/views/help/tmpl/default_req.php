<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$systemInfo = $this->getSystemInfo();
$meetRequirements = true;
$trueM = '<i class="icon-checkmark-2 qx-status-green"></i>';
$falseM = '<i class="icon-cancel qx-status-red"></i>';
?>
<!-- req start -->
<div class="card requirements">
  <div class="card-header">
    <h3 class="card-title">System Requirement</h3>
    <span class="card-title__button" id="requirement_status" style="display: none;">
      <i class="icon-warning"></i> Problem Found
    </span>
    <span class="card-title__button" id="requirement_status_ok" style="display: none;">
      <i class="icon-ok"></i>  Up to date
    </span>
  </div> <!--card-header end-->
  <div class="card-body">
    <ul>
      <li>
        <span class="qx-dash-label"><strong>PHP Version</strong> (min:7.1.x)</span>
        <?php echo version_compare($systemInfo['php_version'], '7.1.0') == -1 ? $falseM : $trueM ?>
        <span class="qx-dash-text <?php echo (version_compare($systemInfo['php_version'], '7.1.0') == -1 ? 'qx-status-red' : '') ?> ">Currently: <?php echo $systemInfo['php_version'] ?></span>
      </li>
      <li>
        <span class="qx-dash-label"><strong>Memory Limit</strong> (min: 64M)</span>
        <?php echo intval($systemInfo['memory_limit']) > 64 ? $trueM : $falseM ?>
        <span class="qx-dash-text">Currently: <?php echo $systemInfo['memory_limit'] ?></span>
      </li>
      
      <li>
        <span class="qx-dash-label"><strong>post_size</strong> (min:5M)</span>
        <?php echo intval($systemInfo['postSize']) < '5' ? $falseM : $trueM ?>
        <span class="qx-dash-text">Currently: <?php echo $systemInfo['postSize'] ?></span>
      </li>

      <li>
        <span class="qx-dash-label"><strong>max_execution</strong> (min:60)</span>
        <?php echo $systemInfo['max_execution'] < '60' ? $falseM : $trueM ?>
        <span class="qx-dash-text">Currently: <?php echo $systemInfo['max_execution'] ?></span>
      </li>

      <li>
        <span class="qx-dash-label"><strong>Cache Folder</strong> <?php echo ($systemInfo['cache_writable'] ? 'Writable' : 'is not writable') ?></span>
        <?php echo $systemInfo['cache_writable'] ? $trueM : $falseM ?>
      </li>
      <li>
        <span class="qx-dash-label"><strong>cURL</strong> <?php echo $systemInfo['curl_support'] ? 'Enabled' : 'is not Enabled' ?></span>
        <?php echo $systemInfo['curl_support'] ? $trueM : $falseM ?>
      </li>

      <li>
        <span class="qx-dash-label"><strong>GD Library</strong> Support</span>
        <?php echo $systemInfo['gd_info'] ? $trueM : $falseM ?>
      </li>
      
      <li>
        <span class="qx-dash-label"><strong>cType</strong> Support</span>
        <?php echo $systemInfo['ctype_support'] ? $trueM : $falseM ?>
      </li>

      <li>
        <span class="qx-dash-label"><strong>Fileinfo</strong> Support</span>
        <?php echo $systemInfo['fileinfo'] ? $trueM : $falseM ?>
      </li>

      <li>
        <span class="qx-dash-label"><strong>Magic Quotes</strong> Disable</span>
        <?php echo $systemInfo['magicQuotes'] ? $falseM : $trueM ?>
      </li>

      <li>
        <span class="qx-dash-label"><strong>allow_url_fopen</strong> Support</span>
        <?php echo $systemInfo['allow_url_fopen'] ? $trueM : $falseM ?>
      </li>

      <li></li>
    </ul>
  </div> <!--card-body end-->
</div> <!--Requirements end-->
<!-- end req start -->
<script type="text/javascript">
  if(jQuery('.requirements .card-body .qx-status-red').length){
    jQuery('.requirements .card-header').addClass('qx-status-red');
    jQuery('#requirement_status').show();
  }else{
    jQuery('.requirements .card-header').addClass('qx-status-green');
    jQuery('#requirement_status_ok').show();
  }
</script>
