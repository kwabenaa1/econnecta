<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined( '_JEXEC' ) or die;
$text = JText::_( 'COM_QUIX_CLEAR_CACHE_TITLE' );
?>
<div style="height: 32px;float: left;">
  <div id="cacheCleanMessage" data-success="Cache cleanded successfully!" style="display: none;"></div>
</div>
<style type="text/css">
  #cacheCleanMessage{
    display: block;
    background: #30c939b8;
    color: #fff;
    font-size: 12px;
    border-radius: 3px;
    line-height: 1;
    padding: 5px;
    margin: 5px;
  }
  .icon-spin {
    -webkit-animation: spin .5s infinite linear;
    animation: spin .5s infinite linear;
  }
  @-webkit-keyframes spin {
  0% {
    -webkit-transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(359deg);
  }
}
@-moz-keyframes spin {
  0% {
    -moz-transform: rotate(0deg);
  }
  100% {
    -moz-transform: rotate(359deg);
  }
}
@-ms-keyframes spin {
  0% {
    -ms-transform: rotate(0deg);
  }
  100% {
    -ms-transform: rotate(359deg);
  }
}
@-o-keyframes spin {
  0% {
    -o-transform: rotate(0deg);
  }
  100% {
    -o-transform: rotate(359deg);
  }
}
@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(359deg);
  }
}
</style>
<a
  onClick="quixClearCache(event)"
  id="clearQuixCache"
  href="javascript:void(0)"
  title="<?php echo $text; ?>" class="btn btn-small btn-danger hasTooltip"
  style="min-width: 0px;margin: 0;text-align: center;">
  <i id="quixcacheicon" class="icon-loop" style="margin: 0px;"></i>
</a>

<script>
  function quixClearCache(e) {
    e.preventDefault();
    jQuery('#cacheCleanMessage').fadeOut('fast');
    jQuery('#quixcacheicon').addClass('icon-spin');
    jQuery.get("index.php?option=com_quix&task=clear_cache", function () {
      // alert("cache cleared :)");
      jQuery('#cacheCleanMessage').html(jQuery('#cacheCleanMessage').data('success'));
      jQuery('#cacheCleanMessage').fadeIn('fast', function () {
        jQuery(this).delay(3000).fadeOut('slow');
      });
      jQuery('#quixcacheicon').removeClass('icon-spin');
    });
  }
</script>
