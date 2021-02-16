<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; 
JHTML::_('behavior.modal');
$app = JFactory::getApplication();

$text = JText::_('COM_QUIX_TOOLBAR_ACTIVATION');
$reviewLater = $app->input->cookie->get('reviewLater', false);
$reviewDone = $app->input->cookie->get('reviewDone', false);
?>
<?php if(!$reviewDone && !$reviewLater): ?>
	<div id="askReview" class="qx-admin-box">
		<div class="display-table">
			<div class="table-icon table-cell table-content">
				<i class="icon-star"></i>
			</div>
			<div class="table-cell table-content">
				<p>Hello! We&rsquo;re really grateful that you&rsquo;re now a part of the ThemeXpert family.</p>

				<p>We hope you&rsquo;re happy with everything Quix has to offer. If you can spare a minute, please help us by leaving a 5-star rating on <a target="_blank" href="https://extensions.joomla.org/extension/quix/">Joomla Extension Directory (JED)</a>. By spreading the love, we can continue to develop new amazing features in the future, for free!</p>

				<p class="action-links">
					<a class="btn btn-warning" href="http://extensions.joomla.org/write-review/review/add?extension_id=11775" target="_blank">
						<i class="icon-link"></i> Ok, You Deserve It
					</a>
					
					<a class="btn" href="#" onclick="reviewLater();">
						<i class="icon-calendar"></i> Nope, Maybe Later
					</a>
					
					<a class="btn" href="#" onclick="reviewDone();">
						<i class="icon-smiley"></i> I Already Did
					</a>

				</p>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function reviewLater(){
			jQuery.ajax({
				url		: 'index.php?option=com_quix&task=get.reviewLater',
				type 	: 'GET',
				beforeSend: function(){
					jQuery('#askReview').hide();
				},
				success: function (res) {
					console.log('Next time. Thank you.');
				}
			});      
		}

		
		function reviewDone(){
			jQuery.ajax({
				url		: 'index.php?option=com_quix&task=get.reviewDone',
				type 	: 'GET',
				beforeSend: function(){
					jQuery('#askReview').hide();
				},
				success: function (res) {
					console.log('Next time. Thank you.');
					alert('Thank you for your feedback.')
				}
			});      
		}
	</script>
<?php endif; ?>