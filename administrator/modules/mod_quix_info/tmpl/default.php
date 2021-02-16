<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_sampledata
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$a = ['banner_bg1.png', 'banner_bg2.png'];
$selected = array_rand($a,1);
$top = false;
?>
<div id="mod_quix_info">
	<?php if(!$isPro): $top = true; ?>
		<div class="freewarning-banner" style="margin: -35px -15px -10px -15px;">
			<a target="_blank" href="https://www.themexpert.com/quix-pagebuilder?utm_medium=button&utm_campaign=quix-pro&utm_source=joomla-admin">
			    <img src="<?php echo JUri::root() . 'libraries/quix/assets/images/' . $a[$selected]; ?>"
			    style="width: 100%;">
		  	</a>
		</div>
	<?php elseif(!$authorise): $top = true; ?>
	<?php
		$text = JText::_('COM_QUIX_TOOLBAR_ACTIVATION');
	?>
		<div class="qx-admin-box pro-activation">
			<div class="display-table">
				<div class="table-cell table-content">
					<img src="<?php echo JUri::root(); ?>libraries/quix/assets/images/quix-logo.png" width="60px">
				</div>
				<div class="table-cell table-content">
					<h4 class="text-uppercase">
						<?php echo JText::_('COM_QUIX_WELCOME'); ?>
					</h4>

					<p>
						<?php echo JText::_('COM_QUIX_AUTHORISE_MESSAGE'); ?>
					</p>
				</div>
				<div class="table-cell">
					<span class="pull-right">	
						<a
						href="index.php?option=com_quix&action=license"
						title="<?php echo $text; ?>" class="quixSettings btn btn-danger btn-small pink"
						>
							<span class="icon-lock"></span> <?php echo $text; ?>
					</a>
					</span>
				</div>
			</div>
		</div>
		<style type="text/css">
			.text-uppercase{
				text-transform: uppercase;
			}
			.qx-admin-box{
				padding: 20px;
				background: #fff;
				box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12), 0 3px 1px -2px rgba(0,0,0,.2);
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
				margin-bottom: 20px;
			    font-size: 14px;
			}
			.display-table{
				display: table;
				width: 100%;
			}
			.table-cell{
				display: table-cell;
			    vertical-align: middle;
			}
			.table-content{
				padding-right:20px; 
			}
			.qx-admin-box{
				margin: -35px -15px -10px -15px;
			    z-index: 1;
			    position: relative;
			}
			#mod_quix_info .btn{
				border: 0;
			    text-shadow: none;
			    border-radius: 2px;
			    letter-spacing: .5px;
			    text-transform: uppercase;
			    margin-top: -3px;
			    color: #fff;
			    font-family: Roboto;
			    font-size: 14px;
			    font-weight: 700;
			    height: 32px;
			    min-height: 32px;
			    line-height: 32px;
			    background-color: #F44336;
			}
		</style>
	<?php endif; ?>

	<?php if($jchOptimized === 'test'): ?>
		<div class="qx-admin-box jch" style="<?php echo ($top ? 'margin-top: 10px;' : '') ?>">
			<div class="display-table">
				<div class="table-cell table-content">
					<img src="<?php echo JUri::root(); ?>libraries/quix/assets/images/quix-logo.png" width="50px">
				</div>
				<div class="table-cell table-content">
					<h4 class="text-uppercase">
						<?php echo JText::_('COM_QUIX_WELCOME'); ?>
					</h4>

					<p>
						<?php echo JText::_('COM_QUIX_AUTHORISE_MESSAGE'); ?>
					</p>
				</div>
			</div>
		</div>
		<style type="text/css">
			.text-uppercase{
				text-transform: uppercase;
			}
			.qx-admin-box{
				padding: 20px;
				background: #fff;
				box-shadow: 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12), 0 3px 1px -2px rgba(0,0,0,.2);
				font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
				margin-bottom: 20px;
			    font-size: 14px;
			}
			.display-table{
				display: table;
				width: 100%;
			}
			.table-cell{
				display: table-cell;
			    vertical-align: middle;
			}
			.table-content{
				padding-right:20px; 
			}
			.qx-admin-box{
				margin: -35px -15px -10px -15px;
			    z-index: 1;
			    position: relative;
			}
			#mod_quix_info .btn{
				border: 0;
			    text-shadow: none;
			    border-radius: 2px;
			    letter-spacing: .5px;
			    text-transform: uppercase;
			    margin-top: -3px;
			    color: #fff;
			    font-family: Roboto;
			    font-size: 14px;
			    font-weight: 700;
			    height: 32px;
			    min-height: 32px;
			    line-height: 32px;
			    background-color: #F44336;
			}
		</style>
	<?php endif; ?>

</div>
